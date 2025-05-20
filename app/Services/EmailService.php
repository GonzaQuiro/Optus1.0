<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\Mailer;

class EmailService
{
    protected $mail = null;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->SMTPDebug = 0;
        $this->mail->Debugoutput = 'html';
        $this->mail->SMTPAuth = true;

        $customer_company_id = isset($_SESSION['customer_company_id']) ? $_SESSION['customer_company_id'] : null;

        
        if ($customer_company_id === null) {
            $this->mail->Host = env('MAIL_HOST');
            $this->mail->Port = env('MAIL_PORT');
            $this->mail->SMTPSecure = env('MAIL_SMTP');
            $this->mail->Username = env('MAIL_USERNAME');
            $this->mail->Password = env('MAIL_PASSWORD');
            $logo = 'images/logo.jpg';

            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $this->mail->ClearAddresses();
            $this->mail->setFrom(env('MAIL_USERNAME'), env('MAIL_ALIAS'));

            $this->mail->Priority = '3';
            $this->mail->Timeout = 600;
            $this->mail->IsHTML(true);
            $this->mail->CharSet = 'UTF-8';
            $this->mail->SMTPKeepAlive = true;

            $this->mail->AddEmbeddedImage(publicPath(asset('/global/img/logo-small.png')), 'front', $logo);
        } else {
            $lst = Mailer::where(
                function ($query) use ($customer_company_id) {
                    $query->where('customer_company_id', '=', $customer_company_id);
                }
            )
                ->get()
                ->first();

            if ($lst) {
                $this->mail->Host = $lst['host'];
                $this->mail->Port = $lst['port'];
                $this->mail->SMTPSecure = $lst['smtp'];
                $this->mail->Username = $lst['user'];
                $this->mail->Password = $lst['pass'];
                $logo = $lst['logo'];

                $this->mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                $this->mail->ClearAddresses();
                
                // Verificar si el campo userAlt es null
                if (is_null($lst['userAlt'])) {
                    $this->mail->setFrom($lst['user'], $lst['alias']);
                } else {
                    $this->mail->setFrom($lst['userAlt'], $lst['alias']);
                }

                $this->mail->Priority = '3';
                $this->mail->Timeout = 600;
                $this->mail->IsHTML(true);
                $this->mail->CharSet = 'UTF-8';
                $this->mail->SMTPKeepAlive = true;
                $this->mail->AddEmbeddedImage(publicPath(asset($lst['logosmall'])), 'front');
            } else {
                $this->mail->Host = env('MAIL_HOST');
                $this->mail->Port = env('MAIL_PORT');
                $this->mail->SMTPSecure = env('MAIL_SMTP');
                $this->mail->Username = env('MAIL_USERNAME');
                $this->mail->Password = env('MAIL_PASSWORD');
                $logo = 'images/logo.jpg';

                $this->mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                $this->mail->ClearAddresses();
                $this->mail->setFrom(env('MAIL_USERNAME'), env('MAIL_ALIAS'));

                $this->mail->Priority = '3';
                $this->mail->Timeout = 600;
                $this->mail->IsHTML(true);
                $this->mail->CharSet = 'UTF-8';
                $this->mail->SMTPKeepAlive = true;

                $this->mail->AddEmbeddedImage(publicPath(asset('/global/img/logo-small.png')), 'front', $logo);
            }
        }
    }

    public function send($message, $subject, $email_to, $alias, $withCC = false)
    {
        $customer_company_id = isset($_SESSION['customer_company_id']) ? $_SESSION['customer_company_id'] : null;

        $result = [
            'success' => false,
            'message' => ''
        ];

        if ($customer_company_id == 7) {
            $result = $this->sendMailOptus($message, $subject, $email_to, $alias);
            return $result;
        }
        
        // Set Recipient
        $this->mail->ClearAddresses();
        foreach ($email_to as $email) {
            $this->mail->addAddress($email);
        }
        if ($withCC) {
            $this->mail->addCC(env('MAIL_CC'));
        }
        // Set Subject
        $this->mail->Subject = $subject;
        // Set Message
        $this->mail->msgHTML($message);
        $this->mail->AltBody = strip_tags($message);
        try {
            if ($this->mail->send()) {
                $result['message'] = 'Mensaje enviado con éxito.';
                $result['success'] = true;
            } else {
                $result['message'] = $this->mail->ErrorInfo;
                $result['success'] = true;
            }
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    public function sendMultiple($emails)
    {
        $results = [];
        foreach ($emails as $emailData) {
            // Verifica que el emailData contenga los datos necesarios
            if (!isset($emailData['message'], $emailData['subject'], $emailData['email_to'], $emailData['alias'])) {
                $results[] = [
                    'success' => false,
                    'message' => 'Datos incompletos para el correo.'
                ];
                continue;
            }

            // Envía cada correo individualmente
            $result = $this->send(
                $emailData['message'],
                $emailData['subject'],
                $emailData['email_to'],
                $emailData['alias'],
                isset($emailData['withCC']) ? $emailData['withCC'] : false
            );
            $results[] = $result;
        }
        return $results;
    }

    private function sendMailOptus($message, $subject, $email_to, $alias)
    {
        // CONFIGURAR LAS VARIABLES CON LOS DATOS DE TU APLICACIÓN DE AZURE
        $tenantId     = env('TENANT_MAILER_TLC');
        $clientId     = env('CLIENT_MAILER_TLC');
        $clientSecret = env('SECRET_MAILER_TLC');

        // 1. OBTENER EL TOKEN DE ACCESO
        $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";
        $tokenData = [
            "client_id"     => $clientId,
            "client_secret" => $clientSecret,
            "scope"         => "https://graph.microsoft.com/.default",
            "grant_type"    => "client_credentials"
        ];
        $ch = curl_init($tokenUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($tokenData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return ['success' => false, 'message' => 'Error al obtener token: '.curl_error($ch)];
        }
        curl_close($ch);

        $responseData = json_decode($response, true);
        if (!isset($responseData['access_token'])) {
            return ['success' => false, 'message' => "Error obteniendo token. Respuesta: $response"];
        }
        $accessToken = $responseData['access_token'];

        // 2. PREPARAR DESTINATARIOS
        $toRecipients = [];
        foreach ($email_to as $recipient) {
            $toRecipients[] = [
                "emailAddress" => [
                    "address" => $recipient
                ]
            ];
        }

        // 3. ENVIAR CORREO CON LA API DE MICROSOFT GRAPH
        $senderEmail = "licitaciones@telecentro.net.ar";
        $mailUrl     = "https://graph.microsoft.com/v1.0/users/{$senderEmail}/sendMail";
        $emailBody   = [
            "message" => [
                "subject"      => $subject,
                "body"         => [
                    "contentType" => "HTML",
                    "content"     => $message
                ],
                "toRecipients" => $toRecipients
            ],
            "saveToSentItems" => true
        ];

        $ch = curl_init($mailUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$accessToken}",
                "Content-Type: application/json"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => json_encode($emailBody),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            curl_close($ch);
            return ['success' => false, 'message' => 'Error enviando correo: '.curl_error($ch)];
        }
        curl_close($ch);

        if ($httpCode === 202) {
            return ['success' => true, 'message' => 'Correo enviado exitosamente.'];
        } else {
            return ['success' => false, 'message' => "Error enviando correo. HTTP $httpCode"];
        }
    }
}
