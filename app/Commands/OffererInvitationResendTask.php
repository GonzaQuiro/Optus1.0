<?php

namespace App\Commands;

use App\Models\User;
use App\Services\EmailService;
use Carbon\Carbon;
use Psr\Container\ContainerInterface;

class OffererInvitationResendTask
{
    const DEFAULT_CUSTOMER_ID = 33;

    private $container;

    private $defaultUserIds = [
        6913, 6914, 6915, 6916, 6917, 6918, 6919, 6920, 6921, 6922, 6923, 6924, 6925, 6926, 6927, 6928, 6929, 6930, 6931, 6932, 6933, 6934, 6935, 6936, 6937, 6938, 6939, 6940, 6941, 6942, 6943, 6944, 6945, 6946, 6947, 6948, 6949, 6950, 6951, 6952, 6953, 6954, 6955, 6956, 6957, 6958, 6959, 6960, 6961, 6962, 6963, 6964, 6965, 6966, 6967, 6968, 6969, 6970, 6971, 6972, 6973, 6974, 6975, 6976, 6977, 6978, 6979, 6980, 6981, 6982, 6983, 6984, 6985, 6986, 6987, 6988, 6989, 6990, 6991, 6992, 6993, 6994, 6995, 6996, 6997, 6998, 6999, 7000, 7001, 7002, 7003, 7004, 7005, 7006, 7007, 7008, 7009, 7010, 7011, 7012, 7013, 7014, 7015, 7016, 7017, 7018, 7019, 7020, 7021, 7022, 7023, 7024, 7025, 7026, 7027, 7028, 7029, 7030, 7031, 7032, 7033, 7034, 7035, 7036, 7037, 7038
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

        try {
            if (!empty($options['memory-limit'])) {
                @ini_set('memory_limit', $options['memory-limit']);
            }

            $userIds = $this->resolveUserIds($options);
            if (empty($userIds)) {
                throw new \RuntimeException('No hay usuarios para procesar.');
            }

            $reportPath = $this->resolveOutputPath($options['result']);
            $this->ensureDirectory(dirname($reportPath));
            $this->printHeader($userIds, $reportPath, $options);

            $report = fopen($reportPath, 'w');
            if ($report === false) {
                throw new \RuntimeException('No se pudo crear el reporte: ' . $reportPath);
            }

            fputcsv($report, [
                'user_id',
                'username',
                'email',
                'offerer_company_id',
                'estado',
                'mensaje',
            ]);

            $stats = [
                'success' => true,
                'total' => 0,
                'sent' => 0,
                'dry_run' => 0,
                'errors' => 0,
                'report' => $reportPath,
            ];

            $limit = $options['limit'] !== null ? (int) $options['limit'] : null;

            foreach ($userIds as $userId) {
                if ($limit !== null && $stats['total'] >= $limit) {
                    break;
                }

                $stats['total']++;
                $result = [
                    'user_id' => $userId,
                    'username' => '',
                    'email' => '',
                    'offerer_company_id' => '',
                    'status' => 'ERROR',
                    'message' => '',
                ];

                try {
                    $user = User::where('id', $userId)
                        ->whereNull('deleted_at')
                        ->first();

                    if (!$user) {
                        throw new \RuntimeException('Usuario no encontrado o eliminado.');
                    }

                    $result['username'] = $user->username;
                    $result['email'] = $user->email;
                    $result['offerer_company_id'] = $user->offerer_company_id;

                    if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        throw new \RuntimeException('Email invalido o vacio.');
                    }

                    if ($options['dry-run']) {
                        $result['status'] = 'DRY_RUN';
                        $result['message'] = 'Dry-run: no se reseteo contrasena ni se envio email.';
                        $stats['dry_run']++;
                    } else {
                        $plainPassword = $this->generatePassword();
                        $user->password = $this->hashPassword($user->username, $plainPassword);
                        $user->save();

                        $emailResult = $this->sendCreationEmail($user, $plainPassword, $options);
                        if (!empty($emailResult['success'])) {
                            $result['status'] = 'ENVIADO';
                            $result['message'] = isset($emailResult['message']) ? $emailResult['message'] : 'Email enviado.';
                            $stats['sent']++;
                        } else {
                            $result['status'] = 'ERROR_EMAIL';
                            $result['message'] = isset($emailResult['message']) ? $emailResult['message'] : 'No se pudo enviar el email.';
                            $stats['errors']++;
                        }
                    }
                } catch (\Throwable $e) {
                    $result['status'] = 'ERROR';
                    $result['message'] = $e->getMessage();
                    $stats['errors']++;
                }

                $this->writeReport($report, $result);
                $this->logRowResult($result);

                if ((int) $options['throttle-ms'] > 0) {
                    usleep((int) $options['throttle-ms'] * 1000);
                }
            }

            fclose($report);
            $this->printSummary($stats);

            return $stats;
        } catch (\Throwable $e) {
            echo "\nERROR: " . $e->getMessage() . "\n";
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function parseArgs(array $args)
    {
        $options = [
            'ids' => null,
            'from-id' => null,
            'to-id' => null,
            'subject' => 'Nuevo usuario Optus',
            'alias' => 'Optus',
            'url' => 'portal.optus.com.ar/login',
            'mail-customer-company-id' => self::DEFAULT_CUSTOMER_ID,
            'dry-run' => false,
            'limit' => null,
            'result' => 'logs/offerer_invitation_resend_' . date('Ymd_His') . '.csv',
            'throttle-ms' => 0,
            'memory-limit' => '512M',
            'help' => false,
        ];

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
            }
        }

        return $options;
    }

    private function resolveUserIds(array $options)
    {
        if (!empty($options['ids'])) {
            $ids = preg_split('/[,\s]+/', (string) $options['ids'], -1, PREG_SPLIT_NO_EMPTY);
            return $this->normalizeUserIds($ids);
        }

        if ($options['from-id'] !== null && $options['to-id'] !== null) {
            $from = (int) $options['from-id'];
            $to = (int) $options['to-id'];
            if ($from > $to) {
                throw new \RuntimeException('--from-id no puede ser mayor a --to-id.');
            }
            return range($from, $to);
        }

        return $this->defaultUserIds;
    }

    private function normalizeUserIds(array $ids)
    {
        $normalized = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $normalized[$id] = $id;
            }
        }

        return array_values($normalized);
    }

    private function sendCreationEmail(User $user, $plainPassword, array $options)
    {
        $html = $this->renderEmail($user, $plainPassword, $options);

        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = [];
        }

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

    private function generatePassword()
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $password = '';

        for ($i = 0; $i < 10; $i++) {
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

    private function writeReport($report, array $result)
    {
        fputcsv($report, [
            $result['user_id'],
            $result['username'],
            $result['email'],
            $result['offerer_company_id'],
            $result['status'],
            $result['message'],
        ]);
    }

    private function printHeader(array $userIds, $reportPath, array $options)
    {
        echo "===========================================\n";
        echo "Reenvio de invitaciones de oferentes\n";
        echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
        echo "Modo: " . ($options['dry-run'] ? 'dry-run' : 'envio real') . "\n";
        echo "Usuarios: " . count($userIds) . "\n";
        echo "IDs: " . implode(',', $userIds) . "\n";
        echo "mail_customer_company_id: " . $options['mail-customer-company-id'] . "\n";
        echo "Reporte: " . $reportPath . "\n";
        echo "===========================================\n\n";
    }

    private function printSummary(array $stats)
    {
        echo "\n===========================================\n";
        echo "Resumen\n";
        echo "Procesados: {$stats['total']}\n";
        echo "Dry-run OK: {$stats['dry_run']}\n";
        echo "Emails enviados: {$stats['sent']}\n";
        echo "Errores: {$stats['errors']}\n";
        echo "Reporte: {$stats['report']}\n";
        echo "===========================================\n";
    }

    private function logRowResult(array $result)
    {
        $this->log(
            "Usuario {$result['user_id']}: {$result['status']} email={$result['email']} mensaje={$result['message']}"
        );
    }

    private function log($message)
    {
        echo '[' . date('H:i:s') . '] ' . $message . "\n";
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

    private function usage()
    {
        return <<<TXT
Uso:
  php crons/resend_offerers_invitations.php --dry-run
  php crons/resend_offerers_invitations.php
  php crons/resend_offerers_invitations.php --ids=4305,4306 --dry-run
  php crons/resend_offerers_invitations.php --from-id=4305 --to-id=4340

Descripcion:
  Reenvia invitaciones a usuarios ya creados, generando una nueva contrasena
  y actualizando el hash antes de enviar el email.

Opciones utiles:
  --dry-run                         Valida usuarios sin resetear contrasena ni enviar emails.
  --ids=4305,4306                   IDs puntuales. Por defecto usa 4305..4340.
  --from-id=4305 --to-id=4340       Rango alternativo de IDs.
  --limit=1                         Procesa solo N usuarios.
  --mail-customer-company-id=33     Contexto SMTP/logo usado por EmailService.
  --subject="Nuevo usuario Optus"   Asunto del email.
  --alias=Optus                     Alias usado por el servicio de email.
  --url=portal.optus.com.ar/login   URL incluida en la plantilla.
  --throttle-ms=500                 Espera entre usuarios.
  --result=logs/reporte.csv         Ruta del reporte CSV sin contrasenas.

TXT;
    }
}
