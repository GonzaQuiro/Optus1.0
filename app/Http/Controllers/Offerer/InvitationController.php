<?php

namespace App\Http\Controllers\Offerer;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Concurso;
use App\Models\Participante;
use App\Models\InvitationStatus;
use Carbon\Carbon;
use App\Models\Step;

class InvitationController extends BaseController
{
    public function acceptOrReject(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $redirect_url = null;

        try {
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();

            $body = json_decode($request->getParsedBody()['Data']);
            
            $action = $body->Action;
            $concurso = Concurso::find((int) $body->IdConcurso);
            $type = $concurso->tipo_concurso;
            $oferente = $concurso->oferentes->where('id_offerer', user()->offerer_company_id)->first();

            switch ($action) {
                case 'accept':
                    if ($concurso->is_go || !$concurso->technical_includes) {
                        $etapa = Participante::ETAPAS['economica-pendiente'];
                        $step = Step::STEPS['offerer']['economica'];
                        $redirect_url = route('concursos.oferente.serveDetail', ['type' => $type, 'step' => $step, 'id' => $concurso->id]);
                    } else {
                        $etapa = Participante::ETAPAS['tecnica-pendiente'];
                        $step = Step::STEPS['offerer']['tecnica'];
                        $redirect_url = route('concursos.oferente.serveDetail', ['type' => $type, 'step' => $step, 'id' => $concurso->id]);
                    }
                    $invitation_status = InvitationStatus::where('code', InvitationStatus::CODES['accepted'])->first();
                    $message = 'Invitación aceptada con éxito.';
                    break;
                case 'reject':
                    $etapa = Participante::ETAPAS['invitacion-rechazada'];
                    $invitation_status = InvitationStatus::where('code', InvitationStatus::CODES['rejected'])->first();
                    $message = 'Invitación rechazada.';
                    $step = Step::STEPS['offerer']['invitacion'];
                    $redirect_url = route('concursos.oferente.serveDetail', ['type' => $type, 'step' => $step, 'id' => $concurso->id]);
                    break;
            }

            $oferente->update([
                'etapa_actual' => $etapa
            ]);

            $invitation = $oferente->invitation;
            $invitation->update([
                'status_id' => $invitation_status->id,
                'comentario_rechazo' => isset($body->reason) ? $body->reason : null
            ]);

            $connection->commit();

        } catch (\Exception $e) {
            $connection->rollBack();
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'redirect' => $redirect_url
            ]
        ], $status);
    }
}