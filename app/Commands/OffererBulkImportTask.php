<?php

namespace App\Commands;

use App\Models\CustomerCompany;
use App\Models\OffererCompany;
use App\Models\User;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Container\ContainerInterface;

class OffererBulkImportTask
{
    const DEFAULT_CREATOR_ID = 7;
    const DEFAULT_CUSTOMER_ID = 33;
    const DEFAULT_USER_TYPE_ID = 6;
    const DEFAULT_STATUS_ID = 1;

    private $container;

    private $requiredColumns = [
        'company_name',
        'cuit',
        'email',
        'user_name',
    ];

    private $optionalColumns = [
        'country',
        'phone',
        'province',
    ];

    private $columnAliases = [
        'company_name' => [
            'nombreempresa',
            'empresa',
            'razonsocial',
            'razonsocialempresa',
            'businessname',
            'nombre',
        ],
        'cuit' => [
            'cuit',
            'cuil',
            'codigo',
            'codigofiscal',
            'taxid',
        ],
        'country' => [
            'pais',
            'country',
        ],
        'email' => [
            'mail',
            'email',
            'correo',
            'correoelectronico',
            'emailusuario',
        ],
        'user_name' => [
            'nombreusuario',
            'usuario',
            'nombrecompleto',
            'contacto',
            'nombre',
        ],
        'phone' => [
            'telefono',
            'telefono',
            'celular',
            'phone',
            'cellphone',
        ],
        'province' => [
            'provincia',
            'province',
            'estado',
        ],
    ];

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function command($args)
    {
        ob_start();
        $this->execute($args);
        return ob_get_clean();
    }

    public function execute(array $args = [])
    {
        @set_time_limit(0);

        $options = $this->parseArgs($args);
        if ($options['help']) {
            echo $this->usage();
            return ['success' => true, 'help' => true];
        }

        if (empty($options['file'])) {
            echo "ERROR: debe indicar el Excel con --file=RUTA o como primer argumento.\n\n";
            echo $this->usage();
            return ['success' => false, 'message' => 'Falta el archivo Excel.'];
        }

        try {
            if (!empty($options['memory-limit'])) {
                @ini_set('memory_limit', $options['memory-limit']);
            }

            $inputPath = $this->resolveInputPath($options['file']);
            $reportPath = $this->resolveOutputPath($options['result']);
            $this->ensureDirectory(dirname($reportPath));
            $this->validateFixedEntities($options);

            $this->printHeader($inputPath, $reportPath, $options);

            // Lectura del Excel y mapeo explicito para soportar los dos encabezados "Nombre".
            $spreadsheet = IOFactory::load($inputPath);
            $sheet = $this->resolveSheet($spreadsheet, $options['sheet']);
            $headerRow = (int) $options['header-row'];
            $columns = $this->resolveColumns($sheet, $headerRow, $options);
            $highestRow = $sheet->getHighestDataRow();

            $report = fopen($reportPath, 'w');
            if ($report === false) {
                throw new \RuntimeException('No se pudo crear el reporte: ' . $reportPath);
            }

            fputcsv($report, [
                'fila_excel',
                'nombre_empresa',
                'cuit',
                'email_usuario',
                'id_empresa',
                'id_usuario',
                'username_generado',
                'estado',
                'motivo_error',
                'estado_email',
                'mensaje_error_email',
            ]);

            $stats = [
                'success' => true,
                'total' => 0,
                'ok' => 0,
                'dry_run' => 0,
                'errors' => 0,
                'emails_sent' => 0,
                'email_errors' => 0,
                'emails_skipped' => 0,
                'report' => $reportPath,
            ];

            $seenCuits = [];
            $seenEmails = [];
            $reservedUsernames = [];
            $limit = $options['limit'] !== null ? (int) $options['limit'] : null;

            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                if ($limit !== null && $stats['total'] >= $limit) {
                    break;
                }

                $data = $this->readOffererRow($sheet, $row, $columns);
                if ($this->isEmptyOffererRow($data)) {
                    continue;
                }

                $stats['total']++;
                $result = $this->emptyRowResult($row, $data);

                if (!$this->validateOffererRow($data, $message)) {
                    $result['status'] = 'ERROR';
                    $result['error_message'] = $message;
                    $this->writeReport($report, $result);
                    $this->log("Fila {$row}: ERROR - {$message}");
                    $stats['errors']++;
                    $stats['emails_skipped']++;
                    continue;
                }

                $cuitKey = $data['normalized_cuit'];
                $emailKey = strtolower($data['email']);

                if (isset($seenCuits[$cuitKey])) {
                    $result['status'] = 'ERROR';
                    $result['error_message'] = 'CUIT duplicado en el Excel. Primera aparicion en fila ' . $seenCuits[$cuitKey] . '.';
                    $this->writeReport($report, $result);
                    $this->log("Fila {$row}: ERROR - {$result['error_message']}");
                    $stats['errors']++;
                    $stats['emails_skipped']++;
                    continue;
                }

                if (isset($seenEmails[$emailKey])) {
                    $result['status'] = 'ERROR';
                    $result['error_message'] = 'Email duplicado en el Excel. Primera aparicion en fila ' . $seenEmails[$emailKey] . '.';
                    $this->writeReport($report, $result);
                    $this->log("Fila {$row}: ERROR - {$result['error_message']}");
                    $stats['errors']++;
                    $stats['emails_skipped']++;
                    continue;
                }

                $seenCuits[$cuitKey] = $row;
                $seenEmails[$emailKey] = $row;

                try {
                    if ($options['dry-run']) {
                        $result = $this->dryRunRow($row, $data, $options, $reservedUsernames);
                        $stats['dry_run']++;
                        $stats['emails_skipped']++;
                    } else {
                        $result = $this->processRow($row, $data, $options, $reservedUsernames);
                        $stats['ok']++;

                        if ($result['email_status'] === 'ENVIADO') {
                            $stats['emails_sent']++;
                        } elseif ($result['email_status'] === 'ERROR_EMAIL') {
                            $stats['email_errors']++;
                        } else {
                            $stats['emails_skipped']++;
                        }
                    }
                } catch (\Throwable $e) {
                    $result = $this->emptyRowResult($row, $data);
                    $result['status'] = 'ERROR';
                    $result['error_message'] = $e->getMessage();
                    $stats['errors']++;
                    $stats['emails_skipped']++;
                }

                $this->writeReport($report, $result);
                $this->logRowResult($result);

                if ((int) $options['throttle-ms'] > 0) {
                    usleep((int) $options['throttle-ms'] * 1000);
                }
            }

            fclose($report);
            $spreadsheet->disconnectWorksheets();

            $this->printSummary($stats);
            return $stats;
        } catch (\Throwable $e) {
            echo "\nERROR: " . $e->getMessage() . "\n";
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function processRow($row, array $data, array $options, array &$reservedUsernames)
    {
        $this->assertNoDatabaseDuplicates($data);

        $connection = dependency('db')->getConnection();
        $connection->beginTransaction();

        try {
            // Transaccion por fila: empresa, usuario, permisos y asociacion quedan atomicos.
            $company = new OffererCompany([
                'status_id' => (int) $options['status-id'],
                'creator_id' => (int) $options['creator-id'],
                'business_name' => $data['company_name'],
                'cuit' => $data['normalized_cuit'],
                'country' => $data['country'] !== '' ? $data['country'] : null,
                'province' => $data['province'] !== '' ? $data['province'] : null,
            ]);
            $company->save();

            $username = $this->generateUniqueUsername($data['user_name'], $reservedUsernames);
            $plainPassword = $this->generatePassword();
            $passwordHash = $this->hashPassword($username, $plainPassword);

            $user = new User([
                'type_id' => (int) $options['user-type-id'],
                'status_id' => (int) $options['status-id'],
                'offerer_company_id' => $company->id,
                'customer_company_id' => null,
                'username' => $username,
                'password' => $passwordHash,
                'first_name' => $data['user_first_name'],
                'last_name' => $data['user_last_name'],
                'phone' => $data['phone'] !== '' ? $data['phone'] : null,
                'cellphone' => $data['phone'] !== '' ? $data['phone'] : null,
                'email' => $data['email'],
            ]);
            $user->save();

            $this->assignOffererPermissions($user);
            $this->associateOffererToCustomer($company->id, (int) $options['customer-id']);

            $connection->commit();
        } catch (\Throwable $e) {
            $connection->rollBack();
            unset($reservedUsernames[$username ?? null]);
            throw $e;
        }

        $result = $this->emptyRowResult($row, $data);
        $result['company_id'] = $company->id;
        $result['user_id'] = $user->id;
        $result['username'] = $username;
        $result['status'] = 'OK';

        // El email se envia despues del commit: un fallo SMTP queda trazado sin revertir datos creados.
        if ($options['no-email']) {
            $result['email_status'] = 'NO_ENVIADO';
            $result['email_message'] = 'Envio deshabilitado por --no-email.';
            return $result;
        }

        $emailResult = $this->sendCreationEmail($user, $plainPassword, $options);
        if (!empty($emailResult['success'])) {
            $result['email_status'] = 'ENVIADO';
            $result['email_message'] = isset($emailResult['message']) ? $emailResult['message'] : 'Email enviado.';
        } else {
            $result['email_status'] = 'ERROR_EMAIL';
            $result['email_message'] = isset($emailResult['message']) ? $emailResult['message'] : 'No se pudo enviar el email.';
        }

        return $result;
    }

    private function dryRunRow($row, array $data, array $options, array &$reservedUsernames)
    {
        $this->assertNoDatabaseDuplicates($data);

        $result = $this->emptyRowResult($row, $data);
        $result['username'] = $this->generateUniqueUsername($data['user_name'], $reservedUsernames);
        $result['status'] = 'OK';
        $result['email_status'] = 'NO_ENVIADO';
        $result['email_message'] = 'Dry-run: no se crearon registros ni se envio email.';

        return $result;
    }

    private function parseArgs(array $args)
    {
        $options = [
            'file' => null,
            'sheet' => 'Hoja1',
            'header-row' => 1,
            'company-name-column' => null,
            'cuit-column' => null,
            'country-column' => null,
            'email-column' => null,
            'user-name-column' => null,
            'phone-column' => null,
            'province-column' => null,
            'creator-id' => self::DEFAULT_CREATOR_ID,
            'customer-id' => self::DEFAULT_CUSTOMER_ID,
            'mail-customer-company-id' => self::DEFAULT_CUSTOMER_ID,
            'user-type-id' => self::DEFAULT_USER_TYPE_ID,
            'status-id' => self::DEFAULT_STATUS_ID,
            'subject' => 'Nuevo usuario Optus',
            'alias' => 'Optus',
            'url' => 'portal.optus.com.ar/login',
            'dry-run' => false,
            'no-email' => false,
            'limit' => null,
            'result' => 'logs/offerer_bulk_import_' . date('Ymd_His') . '.csv',
            'throttle-ms' => 0,
            'memory-limit' => '512M',
            'help' => false,
        ];

        $positionals = [];
        $flags = ['dry-run', 'no-email', 'help'];

        for ($i = 0; $i < count($args); $i++) {
            $arg = trim($args[$i]);
            if ($arg === '') {
                continue;
            }

            if ($arg === '-h' || $arg === '--help') {
                $options['help'] = true;
                continue;
            }

            if ($arg === '-f' && isset($args[$i + 1])) {
                $options['file'] = $args[++$i];
                continue;
            }

            if (substr($arg, 0, 2) === '--') {
                $parts = explode('=', substr($arg, 2), 2);
                $key = strtolower(str_replace('_', '-', $parts[0]));

                if (in_array($key, $flags, true)) {
                    $options[$key] = true;
                    continue;
                }

                $value = isset($parts[1]) ? $parts[1] : null;
                if ($value === null && isset($args[$i + 1]) && substr($args[$i + 1], 0, 1) !== '-') {
                    $value = $args[++$i];
                }

                if (array_key_exists($key, $options)) {
                    $options[$key] = $value;
                }
                continue;
            }

            $positionals[] = $arg;
        }

        if ($options['file'] === null && !empty($positionals)) {
            $options['file'] = $positionals[0];
        }

        return $options;
    }

    private function resolveSheet($spreadsheet, $sheetOption)
    {
        if ($sheetOption === null || $sheetOption === '') {
            return $spreadsheet->getActiveSheet();
        }

        if (is_numeric($sheetOption)) {
            return $spreadsheet->getSheet((int) $sheetOption);
        }

        $sheet = $spreadsheet->getSheetByName($sheetOption);
        if (!$sheet) {
            throw new \RuntimeException('No existe la hoja indicada: ' . $sheetOption);
        }

        return $sheet;
    }

    private function resolveColumns($sheet, $headerRow, array $options)
    {
        $columns = [];
        $headers = [];
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestDataColumn($headerRow));

        for ($column = 1; $column <= $highestColumnIndex; $column++) {
            $label = $this->cellValue($sheet, $column, $headerRow);
            if ($label !== '') {
                $headers[$column] = $this->normalizeHeader($label);
            }
        }

        foreach (array_merge($this->requiredColumns, $this->optionalColumns) as $name) {
            $manualKey = str_replace('_', '-', $name) . '-column';
            if (!empty($options[$manualKey])) {
                $columns[$name] = $this->columnToIndex($options[$manualKey]);
            }
        }

        $nombreColumns = [];
        foreach ($headers as $column => $header) {
            if ($header === 'nombre') {
                $nombreColumns[] = $column;
            }
        }

        if (empty($columns['company_name']) && isset($nombreColumns[0])) {
            $columns['company_name'] = $nombreColumns[0];
        }

        if (empty($columns['user_name']) && isset($nombreColumns[1])) {
            $columns['user_name'] = $nombreColumns[1];
        }

        foreach (array_merge($this->requiredColumns, $this->optionalColumns) as $name) {
            if (!empty($columns[$name])) {
                continue;
            }

            $columns[$name] = $this->findColumn($headers, $name, $columns);
        }

        $missing = [];
        foreach ($this->requiredColumns as $name) {
            if (empty($columns[$name])) {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new \RuntimeException(
                'No se encontraron columnas requeridas: ' . implode(', ', $missing) .
                '. Encabezados detectados: ' . implode(', ', array_values($headers)) .
                '. Para encabezados duplicados puede indicar columnas manuales, por ejemplo ' .
                '--company-name-column=A --cuit-column=B --country-column=C --email-column=D --user-name-column=E --phone-column=F --province-column=G.'
            );
        }

        $this->log('Columnas detectadas: ' . $this->formatColumns($columns));
        return $columns;
    }

    private function findColumn(array $headers, $name, array $usedColumns)
    {
        $aliases = isset($this->columnAliases[$name]) ? $this->columnAliases[$name] : [];
        $normalizedAliases = [];
        foreach ($aliases as $alias) {
            $normalizedAliases[] = $this->normalizeHeader($alias);
        }

        $used = array_filter(array_values($usedColumns));

        foreach ($headers as $column => $header) {
            if (in_array($column, $used, true)) {
                continue;
            }

            if (in_array($header, $normalizedAliases, true)) {
                return $column;
            }
        }

        return null;
    }

    private function readOffererRow($sheet, $row, array $columns)
    {
        $data = [
            'company_name' => $this->normalizeText($this->cellValue($sheet, $columns['company_name'], $row)),
            'cuit_raw' => $this->normalizeText($this->cellValue($sheet, $columns['cuit'], $row)),
            'country' => $this->normalizeText($this->cellValue($sheet, $columns['country'], $row)),
            'email' => $this->normalizeEmail($this->cellValue($sheet, $columns['email'], $row)),
            'user_name' => $this->normalizeText($this->cellValue($sheet, $columns['user_name'], $row)),
            'phone' => '',
            'province' => $this->normalizeText($this->cellValue($sheet, $columns['province'], $row)),
        ];

        if (!empty($columns['phone'])) {
            $data['phone'] = $this->normalizePhone($this->cellValue($sheet, $columns['phone'], $row));
        }

        $data['normalized_cuit'] = $this->normalizeCuit($data['cuit_raw']);

        // Si el Excel no trae nombre de usuario, usamos el nombre de la empresa como contacto seguro.
        if ($data['user_name'] === '') {
            $data['user_name'] = $data['company_name'];
        }

        [$data['user_first_name'], $data['user_last_name']] = $this->splitUserName($data['user_name']);

        return $data;
    }

    private function validateOffererRow(array $data, &$message)
    {
        $requiredLabels = [
            'company_name' => 'Nombre de empresa vacio.',
            'cuit_raw' => 'CUIT vacio.',
            'email' => 'Email de usuario vacio.',
        ];

        foreach ($requiredLabels as $field => $error) {
            if ($data[$field] === '') {
                $message = $error;
                return false;
            }
        }

        if ($data['normalized_cuit'] === '') {
            $message = 'CUIT invalido luego de normalizar.';
            return false;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $message = 'Email invalido.';
            return false;
        }

        if ($this->stringLength($data['company_name']) > 150) {
            $message = 'Nombre de empresa supera 150 caracteres.';
            return false;
        }

        if ($this->stringLength($data['user_first_name']) > 50 || $this->stringLength($data['user_last_name']) > 50) {
            $message = 'Nombre o apellido de usuario supera 50 caracteres.';
            return false;
        }

        $message = 'OK';
        return true;
    }

    private function isEmptyOffererRow(array $data)
    {
        return $data['company_name'] === ''
            && $data['cuit_raw'] === ''
            && $data['country'] === ''
            && $data['email'] === ''
            && $data['user_name'] === ''
            && $data['phone'] === ''
            && $data['province'] === '';
    }

    private function assertNoDatabaseDuplicates(array $data)
    {
        $existingCompany = $this->findOffererByCuit($data['normalized_cuit']);
        if ($existingCompany) {
            throw new \RuntimeException('Ya existe una empresa oferente con ese CUIT. ID existente: ' . $existingCompany->id . '.');
        }

        $existingUser = User::where('email', $data['email'])
            ->whereNull('deleted_at')
            ->first();

        if ($existingUser) {
            throw new \RuntimeException('Ya existe un usuario activo con ese email. ID existente: ' . $existingUser->id . '.');
        }
    }

    private function findOffererByCuit($normalizedCuit)
    {
        if ($normalizedCuit === '') {
            return null;
        }

        return OffererCompany::whereRaw(
            "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(cuit, ' ', ''), '-', ''), '.', ''), ',', ''), '/', '')) = ?",
            [$normalizedCuit]
        )->first();
    }

    private function assignOffererPermissions(User $user)
    {
        $user->permissions()->sync([1, 5, 13]);
    }

    private function associateOffererToCustomer($offererId, $customerId)
    {
        $exists = Capsule::table('offerers_customers')
            ->where('offerer_id', $offererId)
            ->where('customer_id', $customerId)
            ->exists();

        if ($exists) {
            return false;
        }

        Capsule::table('offerers_customers')->insert([
            'offerer_id' => $offererId,
            'customer_id' => $customerId,
        ]);

        return true;
    }

    private function sendCreationEmail(User $user, $plainPassword, array $options)
    {
        $html = $this->renderEmail($user, $plainPassword, $options);

        $hadPreviousContext = array_key_exists('customer_company_id', $_SESSION);
        $previousContext = $hadPreviousContext ? $_SESSION['customer_company_id'] : null;

        if ($options['mail-customer-company-id'] !== null && $options['mail-customer-company-id'] !== '') {
            $_SESSION['customer_company_id'] = (int) $options['mail-customer-company-id'];
        } else {
            unset($_SESSION['customer_company_id']);
        }

        try {
            $emailService = new EmailService();
            return $emailService->send($html, $options['subject'], [$user->email], $options['alias']);
        } finally {
            if ($hadPreviousContext) {
                $_SESSION['customer_company_id'] = $previousContext;
            } else {
                unset($_SESSION['customer_company_id']);
            }
        }
    }

    private function renderEmail(User $user, $plainPassword, array $options)
    {
        $smarty = $this->createSmarty();
        $template = $this->resolveEmailTemplate();

        $smarty->assign('title', $options['subject']);
        $smarty->assign('ano', Carbon::now()->format('Y'));
        $smarty->assign('user', $user);
        $smarty->assign('password', $plainPassword);
        $smarty->assign('url', $options['url']);

        return $smarty->fetch($template);
    }

    private function createSmarty()
    {
        if ($this->container && isset($this->container['view'])) {
            return $this->container['view']->getSmarty();
        }

        $templatesPath = $this->projectPath('resources/views/templates');
        $compileDir = $this->projectPath('storage/tmp/templates_c');
        $cacheDir = $this->projectPath('storage/tmp/cache');

        $this->ensureDirectory($compileDir);
        $this->ensureDirectory($cacheDir);

        $smarty = new \Smarty();
        $smarty->setTemplateDir($templatesPath);
        $smarty->setCompileDir($compileDir);
        $smarty->setCacheDir($cacheDir);

        return $smarty;
    }

    private function resolveEmailTemplate()
    {
        $templates = [
            $this->projectPath('resources/views/templates/email/new-user.tpl'),
            $this->projectPath('resources/views/templates/email/new_user.tpl'),
        ];

        foreach ($templates as $template) {
            if (is_file($template)) {
                return $template;
            }
        }

        throw new \RuntimeException('No se encontro el template email/new-user.tpl ni email/new_user.tpl.');
    }

    private function generateUniqueUsername($fullName, array &$reservedUsernames)
    {
        $base = $this->usernameBase($fullName);

        for ($attempt = 0; $attempt < 200; $attempt++) {
            $digits = (string) random_int(10, 999);
            $candidate = substr($base, 0, 50 - strlen($digits)) . $digits;

            if ($this->usernameIsAvailable($candidate, $reservedUsernames)) {
                $reservedUsernames[$candidate] = true;
                return $candidate;
            }
        }

        for ($counter = 1; $counter <= 9999; $counter++) {
            $suffix = (string) $counter;
            $candidate = substr($base, 0, 50 - strlen($suffix)) . $suffix;

            if ($this->usernameIsAvailable($candidate, $reservedUsernames)) {
                $reservedUsernames[$candidate] = true;
                return $candidate;
            }
        }

        throw new \RuntimeException('No se pudo generar un username unico para el usuario.');
    }

    private function usernameIsAvailable($username, array $reservedUsernames)
    {
        if (isset($reservedUsernames[$username])) {
            return false;
        }

        return !User::where('username', $username)
            ->whereNull('deleted_at')
            ->exists();
    }

    private function usernameBase($fullName)
    {
        $value = $this->asciiLower($fullName);
        $parts = preg_split('/[^a-z0-9]+/', $value, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($parts)) {
            return 'usuario';
        }

        if (count($parts) === 1) {
            $base = $parts[0];
        } else {
            $base = $parts[0] . end($parts);
        }

        $base = preg_replace('/[^a-z0-9]/', '', $base);
        return $base !== '' ? substr($base, 0, 47) : 'usuario';
    }

    private function generatePassword()
    {
        $length = 8;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }

    private function hashPassword($username, $plainPassword)
    {
        $usernameMd5 = md5($username);
        $part1 = substr($usernameMd5, 0, strlen($usernameMd5) / 2);
        $part2 = substr($usernameMd5, strlen($usernameMd5) / 2);

        return hash('sha256', $part2 . $plainPassword . $part1);
    }

    private function splitUserName($fullName)
    {
        $parts = preg_split('/\s+/', trim($fullName), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($parts)) {
            return ['', ''];
        }

        $firstName = array_shift($parts);
        $lastName = trim(implode(' ', $parts));
        if ($lastName === '') {
            $lastName = '-';
        }

        return [
            $this->truncateString($firstName, 50),
            $this->truncateString($lastName, 50),
        ];
    }

    private function normalizeCuit($value)
    {
        $value = trim((string) $value);

        if (preg_match('/^[+-]?\d+(?:[.,]\d+)?E[+-]?\d+$/i', $value)) {
            $value = sprintf('%.0f', (float) str_replace(',', '.', $value));
        }

        $value = strtoupper($value);
        return preg_replace('/[^A-Z0-9]/', '', $value);
    }

    private function normalizePhone($value)
    {
        $value = trim((string) $value);

        if (preg_match('/^[+-]?\d+(?:[.,]\d+)?E[+-]?\d+$/i', $value)) {
            $value = sprintf('%.0f', (float) str_replace(',', '.', $value));
        }

        return $this->normalizeText($value);
    }

    private function normalizeEmail($value)
    {
        $email = strtolower($this->normalizeText($value));
        $email = str_replace(['＠', '﹫', '．', '。', '｡'], ['@', '@', '.', '.', '.'], $email);

        $cleanEmail = preg_replace('/[\p{C}\s]+/u', '', $email);
        if ($cleanEmail === null) {
            $cleanEmail = preg_replace('/[\x00-\x20\x7F]+/', '', $email);
        }
        $email = $cleanEmail;

        if (strpos($email, 'mailto:') === 0) {
            $email = substr($email, 7);
        }

        return trim($email, " \t\n\r\0\x0B<>\"'");
    }

    private function normalizeText($value)
    {
        $value = str_replace("\xC2\xA0", ' ', $value);
        $value = preg_replace('/\s+/', ' ', (string) $value);
        return trim($value);
    }

    private function normalizeHeader($value)
    {
        $value = $this->normalizeText($value);
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        $value = strtolower($value);
        return preg_replace('/[^a-z0-9]/', '', $value);
    }

    private function asciiLower($value)
    {
        $value = $this->normalizeText($value);
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        return strtolower($value);
    }

    private function stringLength($value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value, 'UTF-8');
        }

        return strlen($value);
    }

    private function truncateString($value, $maxLength)
    {
        $value = $this->normalizeText($value);

        if ($this->stringLength($value) <= $maxLength) {
            return $value;
        }

        if (function_exists('mb_substr')) {
            return rtrim(mb_substr($value, 0, $maxLength, 'UTF-8'));
        }

        return rtrim(substr($value, 0, $maxLength));
    }

    private function cellValue($sheet, $column, $row)
    {
        if (empty($column)) {
            return '';
        }

        $value = $sheet->getCellByColumnAndRow((int) $column, (int) $row)->getFormattedValue();
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    private function columnToIndex($column)
    {
        $column = trim((string) $column);
        if ($column === '') {
            return null;
        }

        if (ctype_digit($column)) {
            return (int) $column;
        }

        return Coordinate::columnIndexFromString(strtoupper($column));
    }

    private function formatColumns(array $columns)
    {
        $parts = [];
        foreach ($columns as $name => $column) {
            if (!empty($column)) {
                $parts[] = $name . '=' . Coordinate::stringFromColumnIndex((int) $column);
            }
        }

        return implode(', ', $parts);
    }

    private function emptyRowResult($row, array $data)
    {
        return [
            'row' => $row,
            'company_name' => isset($data['company_name']) ? $data['company_name'] : '',
            'cuit' => isset($data['normalized_cuit']) && $data['normalized_cuit'] !== '' ? $data['normalized_cuit'] : (isset($data['cuit_raw']) ? $data['cuit_raw'] : ''),
            'email' => isset($data['email']) ? $data['email'] : '',
            'company_id' => '',
            'user_id' => '',
            'username' => '',
            'status' => 'ERROR',
            'error_message' => '',
            'email_status' => 'NO_ENVIADO',
            'email_message' => '',
        ];
    }

    private function writeReport($report, array $result)
    {
        fputcsv($report, [
            $result['row'],
            $result['company_name'],
            $result['cuit'],
            $result['email'],
            $result['company_id'],
            $result['user_id'],
            $result['username'],
            $result['status'],
            $result['error_message'],
            $result['email_status'],
            $result['email_message'],
        ]);
    }

    private function validateFixedEntities(array $options)
    {
        $creator = User::where('id', (int) $options['creator-id'])->first();
        if (!$creator) {
            throw new \RuntimeException('No existe creator_id=' . $options['creator-id'] . ' en users.');
        }

        $customer = CustomerCompany::where('id', (int) $options['customer-id'])->first();
        if (!$customer) {
            throw new \RuntimeException('No existe customer_id=' . $options['customer-id'] . ' en customer_companies.');
        }
    }

    private function resolveInputPath($path)
    {
        $path = trim((string) $path, "\"'");
        $candidates = [$path];

        if (!$this->isAbsolutePath($path)) {
            $candidates[] = $this->projectPath($path);
        }

        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real && is_file($real)) {
                return $real;
            }
        }

        throw new \RuntimeException('No se encontro el archivo Excel: ' . $path);
    }

    private function resolveOutputPath($path)
    {
        $path = trim((string) $path, "\"'");
        if ($this->isAbsolutePath($path)) {
            return $path;
        }

        return $this->projectPath($path);
    }

    private function projectPath($path = '')
    {
        $base = dirname(__DIR__, 2);
        if ($path === '') {
            return $base;
        }

        return $base . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($path, '/\\'));
    }

    private function isAbsolutePath($path)
    {
        return (bool) preg_match('/^[A-Za-z]:[\/\\\\]/', $path)
            || substr($path, 0, 1) === '/'
            || substr($path, 0, 1) === '\\';
    }

    private function ensureDirectory($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    private function logRowResult(array $result)
    {
        if ($result['status'] === 'OK') {
            $this->log(
                "Fila {$result['row']}: OK empresa={$result['company_id']} usuario={$result['user_id']} email={$result['email_status']}"
            );
            return;
        }

        $this->log("Fila {$result['row']}: ERROR - {$result['error_message']}");
    }

    private function printHeader($inputPath, $reportPath, array $options)
    {
        echo "===========================================\n";
        echo "Alta masiva de oferentes\n";
        echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
        echo "Excel: " . $inputPath . "\n";
        echo "Hoja: " . $options['sheet'] . "\n";
        echo "Modo: " . ($options['dry-run'] ? 'dry-run' : 'creacion real') . "\n";
        echo "creator_id: " . $options['creator-id'] . "\n";
        echo "customer_id: " . $options['customer-id'] . "\n";
        echo "Reporte: " . $reportPath . "\n";
        echo "===========================================\n\n";
    }

    private function printSummary(array $stats)
    {
        echo "\n===========================================\n";
        echo "Resumen\n";
        echo "Procesados: {$stats['total']}\n";
        echo "OK: {$stats['ok']}\n";
        echo "Dry-run OK: {$stats['dry_run']}\n";
        echo "Errores: {$stats['errors']}\n";
        echo "Emails enviados: {$stats['emails_sent']}\n";
        echo "Errores email: {$stats['email_errors']}\n";
        echo "Emails no enviados: {$stats['emails_skipped']}\n";
        echo "Reporte: {$stats['report']}\n";
        echo "===========================================\n";
    }

    private function log($message)
    {
        echo '[' . date('H:i:s') . '] ' . $message . "\n";
    }

    private function usage()
    {
        return <<<TXT
Uso:
  php crons/import_offerers.php --file=storage/tmp/proveedores.xlsx --dry-run
  php crons/import_offerers.php --file=storage/tmp/proveedores.xlsx
  php public/index.php OffererBulkImportTask --file=storage/tmp/proveedores.xlsx --dry-run

Columnas esperadas en la hoja Hoja1:
  A Nombre empresa, B Cuit, C Pais, D Mail, E Nombre usuario, F Telefono, G Provincia.
  Pais, Telefono y Provincia pueden venir vacios.

Opciones utiles:
  --dry-run                         Valida contra Excel/base y genera reporte sin insertar ni enviar emails.
  --no-email                        Crea empresa/usuario/asociacion, pero no envia emails.
  --sheet=Hoja1                     Hoja a procesar.
  --header-row=1                    Fila de encabezados.
  --company-name-column=A           Columna manual para nombre de empresa.
  --cuit-column=B                   Columna manual para CUIT.
  --country-column=C                Columna manual para pais.
  --email-column=D                  Columna manual para email del usuario.
  --user-name-column=E              Columna manual para nombre del usuario.
  --phone-column=F                  Columna manual para telefono/celular.
  --province-column=G               Columna manual para provincia.
  --creator-id=7                    Creador de offerer_companies.
  --customer-id=42                  Cliente fijo para offerers_customers.
  --mail-customer-company-id=42     Contexto SMTP/logo usado por EmailService.
  --subject="Nuevo usuario Optus"   Asunto del email.
  --alias=Optus                     Alias usado por el servicio de email.
  --url=portal.optus.com.ar/login   URL incluida en la plantilla.
  --limit=10                        Procesa solo N filas no vacias.
  --throttle-ms=500                 Espera entre filas procesadas.
  --result=logs/reporte.csv         Ruta del reporte CSV sin contrasenas.

TXT;
    }
}
