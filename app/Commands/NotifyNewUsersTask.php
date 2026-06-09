<?php

namespace App\Commands;

use App\Services\EmailService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Container\ContainerInterface;

class NotifyNewUsersTask
{
    private $container;

    private $requiredColumns = ['email', 'username', 'password'];

    private $optionalColumns = ['first_name', 'last_name', 'customer_company_id'];

    private $columnAliases = [
        'email' => [
            'email',
            'mail',
            'correo',
            'correoelectronico',
            'correoelectrnico',
            'emailusuario',
        ],
        'username' => [
            'username',
            'usuario',
            'usuarios',
            'user',
            'login',
            'nombreusuario',
            'nombredeusuario',
        ],
        'password' => [
            'password',
            'pass',
            'clave',
            'contrasena',
            'contrasea',
            'contrasenia',
            'passwordusuario',
        ],
        'first_name' => [
            'firstname',
            'first_name',
            'nombre',
            'nombres',
            'name',
        ],
        'last_name' => [
            'lastname',
            'last_name',
            'apellido',
            'apellidos',
            'surname',
        ],
        'customer_company_id' => [
            'customercompanyid',
            'customer_company_id',
            'idcliente',
            'idclientecustomer',
            'idempresacliente',
            'empresaclienteid',
            'clienteid',
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
            $inputPath = $this->resolveInputPath($options['file']);
            $reportPath = $this->resolveOutputPath($options['result']);
            $this->ensureDirectory(dirname($reportPath));

            $this->printHeader($inputPath, $reportPath, $options);

            $spreadsheet = IOFactory::load($inputPath);
            $sheet = $this->resolveSheet($spreadsheet, $options['sheet']);
            $headerRow = (int) $options['header-row'];
            $columns = $this->resolveColumns($sheet, $headerRow, $options);
            $highestRow = $sheet->getHighestDataRow();

            $report = fopen($reportPath, 'w');
            if ($report === false) {
                throw new \RuntimeException('No se pudo crear el reporte: ' . $reportPath);
            }

            fputcsv($report, ['row', 'email', 'username', 'customer_company_id', 'status', 'message']);

            $stats = [
                'success' => true,
                'total' => 0,
                'sent' => 0,
                'dry_run' => 0,
                'skipped' => 0,
                'errors' => 0,
                'report' => $reportPath,
            ];

            $seenEmails = [];
            $limit = $options['limit'] !== null ? (int) $options['limit'] : null;

            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                if ($limit !== null && $stats['total'] >= $limit) {
                    break;
                }

                $data = $this->readUserRow($sheet, $row, $columns);
                if ($this->isEmptyUserRow($data)) {
                    continue;
                }

                $stats['total']++;
                $emailKey = strtolower($data['email']);

                if (!$this->validateUserRow($data, $message)) {
                    $this->writeReport($report, $row, $data, 'skipped', $message);
                    $this->log("Fila {$row}: omitida - {$message}");
                    $stats['skipped']++;
                    continue;
                }

                if (isset($seenEmails[$emailKey])) {
                    $message = 'Email duplicado en el Excel.';
                    $this->writeReport($report, $row, $data, 'skipped', $message);
                    $this->log("Fila {$row}: omitida - {$message}");
                    $stats['skipped']++;
                    continue;
                }

                $seenEmails[$emailKey] = true;

                if ($options['dry-run']) {
                    $this->renderEmail($data, $options);
                    $message = 'Validado en dry-run. No se envio email.';
                    $this->writeReport($report, $row, $data, 'dry-run', $message);
                    $this->log("Fila {$row}: dry-run OK para {$data['email']}");
                    $stats['dry_run']++;
                    continue;
                }

                $result = $this->sendEmail($data, $options);
                if (!empty($result['success'])) {
                    $message = isset($result['message']) ? $result['message'] : 'Email enviado.';
                    $this->writeReport($report, $row, $data, 'sent', $message);
                    $this->log("Fila {$row}: enviado a {$data['email']}");
                    $stats['sent']++;
                } else {
                    $message = isset($result['message']) ? $result['message'] : 'No se pudo enviar.';
                    $this->writeReport($report, $row, $data, 'error', $message);
                    $this->log("Fila {$row}: ERROR para {$data['email']} - {$message}");
                    $stats['errors']++;
                }

                if ((int) $options['throttle-ms'] > 0) {
                    usleep((int) $options['throttle-ms'] * 1000);
                }
            }

            fclose($report);
            $spreadsheet->disconnectWorksheets();

            $this->printSummary($stats);
            return $stats;
        } catch (\Exception $e) {
            echo "\nERROR: " . $e->getMessage() . "\n";
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function parseArgs(array $args)
    {
        $options = [
            'file' => null,
            'sheet' => null,
            'header-row' => 1,
            'email-column' => null,
            'username-column' => null,
            'password-column' => null,
            'first-name-column' => null,
            'last-name-column' => null,
            'customer-company-id-column' => null,
            'customer-company-id' => null,
            'subject' => 'Nuevo usuario Optus',
            'alias' => 'Optus',
            'url' => 'portal.optus.com.ar/login',
            'dry-run' => false,
            'limit' => null,
            'result' => 'logs/new_user_notifications_' . date('Ymd_His') . '.csv',
            'throttle-ms' => 0,
            'help' => false,
        ];

        $positionals = [];
        $flags = ['dry-run', 'help'];

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
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestDataColumn($headerRow));
        $headers = [];

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
                continue;
            }

            $columns[$name] = $this->findColumn($headers, $name);
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
                ". Encabezados detectados: " . implode(', ', array_values($headers)) .
                ". Tambien puede indicar columnas manuales, por ejemplo --email-column=C --username-column=B --password-column=D."
            );
        }

        $this->log('Columnas detectadas: ' . $this->formatColumns($columns));
        return $columns;
    }

    private function findColumn(array $headers, $name)
    {
        $aliases = isset($this->columnAliases[$name]) ? $this->columnAliases[$name] : [];
        $normalizedAliases = [];
        foreach ($aliases as $alias) {
            $normalizedAliases[] = $this->normalizeHeader($alias);
        }

        foreach ($headers as $column => $header) {
            if (in_array($header, $normalizedAliases, true)) {
                return $column;
            }
        }

        return null;
    }

    private function readUserRow($sheet, $row, array $columns)
    {
        $data = [
            'email' => $this->cellValue($sheet, $columns['email'], $row),
            'username' => $this->cellValue($sheet, $columns['username'], $row),
            'password' => $this->cellValue($sheet, $columns['password'], $row),
            'first_name' => '',
            'last_name' => '',
            'customer_company_id' => '',
        ];

        if (!empty($columns['first_name'])) {
            $data['first_name'] = $this->cellValue($sheet, $columns['first_name'], $row);
        }

        if (!empty($columns['last_name'])) {
            $data['last_name'] = $this->cellValue($sheet, $columns['last_name'], $row);
        }

        if (!empty($columns['customer_company_id'])) {
            $data['customer_company_id'] = $this->cellValue($sheet, $columns['customer_company_id'], $row);
        }

        return $data;
    }

    private function validateUserRow(array $data, &$message)
    {
        if ($data['email'] === '') {
            $message = 'Email vacio.';
            return false;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $message = 'Email invalido.';
            return false;
        }

        if ($data['username'] === '') {
            $message = 'Usuario vacio.';
            return false;
        }

        if ($data['password'] === '') {
            $message = 'Contrasena vacia.';
            return false;
        }

        $message = 'OK';
        return true;
    }

    private function isEmptyUserRow(array $data)
    {
        return $data['email'] === '' && $data['username'] === '' && $data['password'] === '';
    }

    private function sendEmail(array $data, array $options)
    {
        $html = $this->renderEmail($data, $options);
        $this->applyCustomerCompanyContext($data, $options);

        $emailService = new EmailService();
        return $emailService->send($html, $options['subject'], [$data['email']], $options['alias']);
    }

    private function renderEmail(array $data, array $options)
    {
        $smarty = $this->createSmarty();
        $template = $this->projectPath('resources/views/templates/email/new-user.tpl');
        $user = (object) [
            'first_name' => $data['first_name'] !== '' ? $data['first_name'] : $data['username'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
        ];

        $smarty->assign('title', $options['subject']);
        $smarty->assign('ano', Carbon::now()->format('Y'));
        $smarty->assign('user', $user);
        $smarty->assign('password', $data['password']);
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

    private function applyCustomerCompanyContext(array $data, array $options)
    {
        $customerCompanyId = $options['customer-company-id'] !== null && $options['customer-company-id'] !== ''
            ? $options['customer-company-id']
            : $data['customer_company_id'];

        if ($customerCompanyId !== null && $customerCompanyId !== '') {
            $_SESSION['customer_company_id'] = (int) $customerCompanyId;
            return;
        }

        unset($_SESSION['customer_company_id']);
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

    private function normalizeHeader($value)
    {
        $value = trim((string) $value);
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        $value = strtolower($value);
        return preg_replace('/[^a-z0-9]/', '', $value);
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

    private function writeReport($report, $row, array $data, $status, $message)
    {
        fputcsv($report, [
            $row,
            $data['email'],
            $data['username'],
            $data['customer_company_id'],
            $status,
            $message,
        ]);
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

    private function printHeader($inputPath, $reportPath, array $options)
    {
        echo "===========================================\n";
        echo "Notificacion de nuevos usuarios\n";
        echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
        echo "Excel: " . $inputPath . "\n";
        echo "Modo: " . ($options['dry-run'] ? 'dry-run' : 'envio real') . "\n";
        echo "Reporte: " . $reportPath . "\n";
        echo "===========================================\n\n";
    }

    private function printSummary(array $stats)
    {
        echo "\n===========================================\n";
        echo "Resumen\n";
        echo "Procesados: {$stats['total']}\n";
        echo "Enviados: {$stats['sent']}\n";
        echo "Dry-run: {$stats['dry_run']}\n";
        echo "Omitidos: {$stats['skipped']}\n";
        echo "Errores: {$stats['errors']}\n";
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
  php crons/notify_new_users.php --file=storage/tmp/usuarios.xlsx --dry-run
  php crons/notify_new_users.php --file=storage/tmp/usuarios.xlsx
  php public/index.php NotifyNewUsersTask --file=storage/tmp/usuarios.xlsx --dry-run

Columnas requeridas por encabezado:
  email/correo/mail, usuario/username, password/contrasena/clave.

Columnas opcionales:
  nombre, apellido, customer_company_id.

Opciones utiles:
  --dry-run                         Valida y renderiza sin enviar emails.
  --sheet=NOMBRE_O_INDICE           Usa una hoja especifica.
  --header-row=1                    Fila de encabezados.
  --email-column=C                  Columna manual para email.
  --username-column=B               Columna manual para usuario.
  --password-column=D               Columna manual para contrasena.
  --first-name-column=E             Columna manual para nombre.
  --customer-company-id=7           Fuerza el SMTP/logo de una empresa cliente.
  --subject="Nuevo usuario Optus"   Asunto del email.
  --alias=Optus                     Alias usado por el servicio de email.
  --url=portal.optus.com.ar/login   URL incluida en la plantilla.
  --limit=10                        Procesa solo N usuarios.
  --throttle-ms=500                 Espera entre envios.
  --result=logs/reporte.csv         Ruta del reporte CSV sin contrasenas.

TXT;
    }
}
