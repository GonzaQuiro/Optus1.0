<?php

namespace App\Http\Controllers\Solped;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Solped;
use App\Services\EmailService;
use Carbon\Carbon;

class SolpedInvitationController extends BaseController
{
    public function send(Request $request, Response $response)
    {
        $success = false;
        $message = '';
        $status = 200;

        try {
            $body = $request->getParsedBody();

            // Buscar la solicitud
            $solped = Solped::find((int)$body['IdSolicitud']);
            if (!$solped) {
                throw new \Exception('Solicitud no encontrada.');
            }

            // Cambiar estado de la solicitud
            $solped->estado_actual = 'esperando-revision';
            $solped->save();

            // Obtener el comprador sugerido o todos los compradores de la empresa
            $users = [];
            $companyName = 'Empresa';

            // Intentar obtener el comprador sugerido
            if ($solped->id_comprador_sugerido) {
                $comprador = $solped->comprador_sugerido;
                if ($comprador && $comprador->email) {
                    $users = [$comprador->email];
                    if ($comprador->customer_company) {
                        $companyName = $comprador->customer_company->business_name ?? 'Empresa';
                    }
                }
            }

            // Si no hay comprador sugerido, obtener todos los compradores de la empresa
            if (empty($users)) {
                $compradores = $solped->compradores;
                $users = $compradores->pluck('email')->toArray();
                if (!empty($compradores) && $compradores->first()) {
                    $companyName = $compradores->first()->customer_company->business_name ?? 'Empresa';
                }
            }

            if (empty($users)) {
                throw new \Exception('No hay usuarios disponibles para notificar.');
            }

            // Preparar correo
            $title = 'Nueva Solicitud de Pedido para Revisión';
            $subject = $companyName . ' - ' . $title;
            $template = rootPath(config('app.templates_path')) . '/email/solped-sent.tpl';

            $emailService = new EmailService();
            $html = $this->fetch($template, [
                'compradorNombre' => !empty($users) ? (isset($comprador) ? $comprador->full_name : 'Comprador') : 'Comprador',
                'nombreSolicitud' => $solped->nombre,
                'areaSolicitante' => $solped->area_sol,
                'fechaResolucion' => $solped->fecha_resolucion ? (new \DateTime($solped->fecha_resolucion))->format('d-m-Y H:i') : '-',
                'enlaceAcceso' => 'https://' . $_SERVER['HTTP_HOST'] . '/solped/edicion/' . $solped->id
            ]);

            // Enviar correo
            $result = $emailService->send($html, $subject, $users, "");

            $success = $result['success'];
            $message = $success ? 'Notificación enviada correctamente.' : 'Error al enviar la notificación.';

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message
        ], $status);
    }
}
