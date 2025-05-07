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


        $result = [
            'success' => false,
            'message' => ''
        ];

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
}
