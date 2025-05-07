<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;
use App\Services\EmailService;
use \Exception as Exception;

class ConsultaController extends BaseController
{
    public function serveCreate(Request $request, Response $response, $params)
    {
        return $this->render($response, 'consultas/edit.tpl', [
            'page'      => 'consultas',
            'accion'    => 'nueva-consulta',
            'title'     => 'Nueva Consulta'
        ]);
    }

    public function send(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];
        $redirect_url = null;

        try {
            abort_if($request, $response, isAdmin(), true, 404);

            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();

            $body = json_decode($request->getParsedBody()['Data']);

            $fields = [
                'consulta'  => $body->Consulta
            ];

            $user = user();
            $usuario = $user->full_name;
            $consulta = $body->Consulta; 

            if (isOfferer()) {
                $tipo = 'Oferente';
                $nombre = $user->offerer_company->business_name;

            } elseif (isCustomer()) {
                $tipo = 'Cliente';
                $nombre = $user->customer_company->business_name;
            }

            $title = 'Mesa de ayuda';
            $cliente = $tipo . ' ' . $nombre;
            $subject = $cliente . ' - ' . $title;
            $template = rootPath(config('app.templates_path')) . '/email/helpdesk.tpl';
            $emailService = new EmailService();
            $html = $this->fetch($template, [
                'title'         => $title,
                'cliente'       => $cliente,
                'consulta'      => $consulta,
                'user'          => $user
            ]);

            $result = $emailService->send(
                $html,
                $subject, 
                [env('MAIL_HELPDESK')], 
                $user->full_name);

            $redirect_url = route('consultas.serveCreate');
            $success = true;
            $message = 'Consulta enviada con éxito.';

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'redirect'  => $redirect_url
            ]
        ], $status);
    }

}