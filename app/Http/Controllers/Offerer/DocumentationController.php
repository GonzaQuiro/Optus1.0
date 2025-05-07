<?php

namespace App\Http\Controllers\Offerer;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Concurso;
use App\Models\Participante;
use App\Services\DocumentService;
use App\Services\EmailService;
use Carbon\Carbon;

class DocumentationController extends BaseController
{
    public function check(Request $request, Response $response, $params)
    {
        $success = false;
        $status = 200;
        $message = '';

        try {
            $body = json_decode($request->getParsedBody()['Entity']);
            $id_concurso = $body->Id;
            $driver_selected = $body->DriverSelected;
            $vehicle_selected = $body->VehicleSelected;
            $trailer_selected = $body->TrailerSelected;

            if ($driver_selected || $vehicle_selected || $trailer_selected) {
                $id_usuario = user()->id;
        
                $concurso = Concurso::find($id_concurso);
        
                $oferente = Participante::where([
                    ['id_usuario', $id_usuario],
                    ['id_concurso', $concurso->id]
                ])->first();
                
                $documentService = new DocumentService();

                $validation = json_decode($documentService->getDocumentation(
                    $concurso,
                    $driver_selected,
                    $vehicle_selected,
                    $trailer_selected
                ));

                $success = true;
                $message = 'OK';
                $status = 200;
            }

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => $validation->data
        ], $status);
    }

    public function sendReminder(Request $request, Response $response)
    {
        $success = false;
        $message = '';
        $status = 200;
        $result = [];

        try {
            $emailService = new EmailService();

            $body = $request->getParsedBody();
            $oferente = json_decode($body['Oferente'], true);
            $concurso = Concurso::find((int) $body['IdConcurso']);
            $oferenteModel = Participante::find($oferente['id']);

            $nombre = $oferenteModel->user->full_name;
            $business_name = $oferenteModel->user->offerer_company->business_name;
            $email = $oferenteModel->user->email;
            $subject = 'Documentación adeudada concurso ' . $concurso->nombre;
            $alias = $nombre . ' (' . $business_name . ')';
            $driver_documents = [];
            $vehicle_documents = [];
            $trailer_documents = [];

            foreach ($oferente['documents']['go_driver_documents'] as $document) {
                if (filter_var($document['success'], FILTER_VALIDATE_BOOLEAN)) {
                    continue;
                }
                $driver_documents[] = $document;
            }

            foreach ($oferente['documents']['go_vehicle_documents'] as $document) {
                if (filter_var($document['success'], FILTER_VALIDATE_BOOLEAN)) {
                    continue;
                }
                $vehicle_documents[] = $document;
            }

            foreach ($oferente['documents']['go_trailer_documents'] as $document) {
                if (filter_var($document['success'], FILTER_VALIDATE_BOOLEAN)) {
                    continue;
                }
                $trailer_documents[] = $document;
            }

            $template = rootPath(config('app.templates_path')) . '/email/documentation-reminder.tpl';

            $html = $this->fetch($template, [
                'title'                 => 'Documentación adeudada del concurso "' . $concurso->nombre . '"',
                'ano'                   => Carbon::now()->format('Y'),
                'concurso'              => $concurso,
                'oferente'              => $oferenteModel,
                'driver_description'    => $oferente['driver_description'],
                'driver_documents'      => $driver_documents,
                'vehicle_description'   => $oferente['vehicle_description'],
                'vehicle_documents'     => $vehicle_documents,
                'trailer_documents'     => $trailer_documents,
                'trailer_description'   => $oferente['trailer_description']
            ]);

            $result = $emailService->send($html, $subject, $email, $alias, true);

            $message = $result['message'];
            $success = $result['success'];

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
        ], $status);
    }
}