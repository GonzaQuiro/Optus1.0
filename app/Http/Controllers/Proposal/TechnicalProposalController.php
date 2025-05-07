<?php

namespace App\Http\Controllers\Proposal;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Concurso;
use App\Models\Step;
use App\Models\Participante;
use App\Models\ParticipanteGoDocument;
use App\Models\Proposal;
use App\Models\ProposalType;
use App\Models\ProposalStatus;
use App\Models\ProposalDocument;
use App\Services\EmailService;
use Carbon\Carbon;
use \Exception as Exception;
use App\Models\User;

class TechnicalProposalController extends BaseController
{
    public function send(Request $request, Response $response)
    {

        $success = false;
        $message = '';
        $status = 200;
        $result = [];
        $redirect_url = null;

        try {
            $body = json_decode($request->getParsedBody()['Entity']);
            $concursoId = json_decode($request->getParsedBody()['ConcursoId']);
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();
            $emailService = new EmailService();

            $user = user();
            $concurso = Concurso::find($concursoId);
            $oferente = $concurso->oferentes->where('id_offerer', $user->offerer_company_id)->first();
            $rondaTecnicaOferente = $oferente->ronda_tecnica;

            if ($concurso->is_go) {
                $etapa = Participante::ETAPAS['economica-pendiente'];
                $redirect_url = route('concursos.oferente.serveDetail', [
                    'id' => $concurso->id,
                    'type' => Concurso::TYPES[$concurso->tipo_concurso],
                    'step' => Step::STEPS['offerer']['economica']
                ]);
                $fields = [];
            } else {
                $etapa = $rondaTecnicaOferente == 1 ? Participante::ETAPA_TECNICA_PRESENTADA['tecnica-presentada']:Participante::ETAPA_TECNICA_PRESENTADA['tecnica-presentada-'.$rondaTecnicaOferente];
            }

            if (!$concurso->is_go) {
                $fields = [
                    'comment' => $body->comentario
                ];
            }

            $validator = $this->validate($body, $fields);

            if ($validator->fails()) {
                $success = false;
                $message = $validator->errors()->first();
                $status = 422;
            } else {
                $technical_proposal = $oferente->technical_proposal;
                if (!$technical_proposal) {
                    $proposal_status =
                        $concurso->is_go ?
                        ProposalStatus::where('code', ProposalStatus::CODES['accepted'])->first() :
                        ProposalStatus::where('code', ProposalStatus::CODES['pending'])->first();
                    $proposal_type = ProposalType::where('code', ProposalType::CODES['technical'])->first();
                    $technical_proposal = new Proposal([
                        'participante_id' => $oferente->id,
                        'status_id' => $proposal_status->id,
                        'type_id' => $proposal_type->id,
                        'ronda_tecnica' => $oferente->ronda_tecnica
                    ]);
                    $technical_proposal->save();
                    $technical_proposal->refresh();
                }
                $technical_proposal->update($fields);
                $oferente->refresh();

                $fields_offerer = [
                    'etapa_actual' => $etapa
                ];
                if ($concurso->is_go) {
                    $fields_offerer = array_merge($fields_offerer, [
                        'id_conductor' => "test",
                        'id_vehiculo' => "test",
                        'id_trailer' => "test"
                    ]);
                }
                $oferente->update($fields_offerer);
                $oferente->refresh();

                $result = $this->updateDocuments($concurso, $oferente, $body);
                
                if ($result['success']) {
                    // Genera los mensajes desde los templates
                    $template1 = rootPath(config('app.templates_path')) . '/email/proposal-send.tpl';
                    $message1 = $this->fetch($template1, [
                        'concurso' => $concurso,
                        'title' => 'Propuesta Técnica',
                        'ano' => Carbon::now()->format('Y'),
                        'cliente' => $concurso->cliente->customer_company->business_name,
                        'proveedor' => $user->offerer_company->business_name,
                        'nuevaRonda' => Participante::RONDAS[$oferente->ronda_tecnica] . ' Técnica',
                    ]);
                
                    $template2 = rootPath(config('app.templates_path')) . '/email/technical-confirmation.tpl';
                    $message2 = $this->fetch($template2, [
                        'concurso' => $concurso,
                        'title' => 'Confirmacion propuesta técnica',
                        'ano' => Carbon::now()->format('Y'),
                        'cliente' => $concurso->cliente->customer_company->business_name,
                        'proveedor' => $user->offerer_company->business_name,
                    ]);
                
                    // Prepara los correos
                    $emails = [
                        [
                            'message' => $message1,
                            'subject' => $concurso->nombre . ' - Propuesta Técnica',
                            'email_to' => array_merge(
                                array_map([$this, 'getEmailUser'], $concurso->ficha_tecnica_usuario_evalua ? explode(',', $concurso->ficha_tecnica_usuario_evalua) : []),
                                [$concurso->cliente->email]
                            ),
                            'alias' => '',
                        ],
                        [
                            'message' => $message2,
                            'subject' => $concurso->nombre . ' - Confirmacion propuesta técnica',
                            'email_to' => [$user->email], // Cambia por los correos reales de los administradores
                            'alias' => '',
                        ],
                    ];
                
                    // Envía los correos
                    $results = $emailService->sendMultiple($emails);
                
                    // Manejo de resultados
                    foreach ($results as $res) {
                        if (!$res['success']) {
                            $success = false;
                            $message = $res['message'];
                            $status = 422;
                            break;
                        }
                    }
                
                    $success = true;
                } else {
                    $success = false;
                    $message = $result['message'];
                    $status = 422;
                }
                
                
            }

            if ($success) {
                $connection->commit();
                $message =
                    ($concurso->is_go ?
                        'Documentación enviada con éxito.' :
                        'Propuesta enviada con éxito.');
            } else {
                $connection->rollBack();
            }

        } catch (Exception $e) {
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

    public function update(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $redirect_url = null;

        try {
            $body = json_decode($request->getParsedBody()['Entity']);
            $concursoId = json_decode($request->getParsedBody()['ConcursoId']);
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();

            $user = user();
            $concurso = Concurso::find($concursoId);
            $oferente = $concurso->oferentes->where('id_offerer', $user->offerer_company_id)->first();
            

            if ($concurso->is_go) {
                $redirect_url = route('concursos.oferente.serveDetail', [
                    'id' => $concurso->id,
                    'type' => Concurso::TYPES[$concurso->tipo_concurso],
                    'step' => Step::STEPS['offerer']['economica']
                ]);
            }

            if ($concurso->is_go) {
                $fields = [
                    'comment' => ''
                ];
            } else {
                $fields = [
                    'comment' => $body->comentario
                ];
            }

            $validator = $this->validate($body, $fields);
            
            if ($validator->fails()) {
                $success = false;
                $message = $validator->errors()->first();
                $status = 422;
            } else {
                $technical_proposal = $oferente->technical_proposal;
                if (!$technical_proposal) {
                    $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['pending'])->first();
                    $proposal_type = ProposalType::where('code', ProposalType::CODES['technical'])->first();
                    $technical_proposal = new Proposal([
                        'participante_id' => $oferente->id,
                        'status_id' => $proposal_status->id,
                        'type_id' => $proposal_type->id,
                        'ronda_tecnica' => $oferente->ronda_tecnica
                    ]);
                    $technical_proposal->save();
                    $technical_proposal->refresh();
                }
                $technical_proposal->update($fields);
                $oferente->refresh();
                
                
                $result = $this->updateDocuments($concurso, $oferente, $body);

                if ($result['success']) {
                    $success = true;
                } else {
                    $success = false;
                    $message = $result['message'];
                    $status = 422;
                }
            }

            if ($success) {
                $connection->commit();
                $message =
                    $concurso->is_go ?
                    'Documentación actualizada con éxito.' :
                    'Propuesta actualizada con éxito.';
            } else {
                $connection->rollBack();
            }

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

    public function acceptOrReject(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $error = false;

        try {
            $body = json_decode($request->getParsedBody()['Data']);
            $concurso = Concurso::find($body->IdConcurso);
            $oferente = $concurso->oferentes->find($body->Calificacion[0]->UserId);
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();
            $emailService = new EmailService();

            $calification = $body->Calificacion;
            $minimo = (int) $calification[0]->minimo;
            $alcanzado = (int) $calification[0]->alcanzado;
            $accepted = $alcanzado >= $minimo ? true : false;

            if ($accepted) {
                $etapa = Participante::ETAPAS['economica-pendiente'];
                $rejected = false;
                $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['accepted'])->first();
            } else {
                $etapa = $oferente->etapa_actual;
                $rejected = true;
                $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['rejected'])->first();
                $calification[0]->comentario = $body->comentario;
            }

            $fields = [
                'calification' => []
            ];

            $payroll_items = $concurso->plantilla_tecnica->parsed_items;
            $values = explode(',', $calification[0]->valores);
            foreach ($payroll_items as $index => $payroll_item) {
                if ($index == 0) {
                    continue;
                }
                $fields['calification'][] = [
                    'name' => $payroll_item->atributo,
                    'ponderacion' => (int) $payroll_item->ponderacion,
                    'value' => $values[$index - 1] ? (int) $values[$index - 1] : null,
                    'comentario' => isset($body->comentario) ? $body->comentario : null
                ];
            }

            $validation = $this->validateCalification($body, $concurso, $fields);
            if ($validation->fails()) {
                $error = true;
                $status = 422;
                $message = $validation->errors()->first();
            } else {
                // DDBB Update
                $technical_proposal = $oferente->technical_proposal;
                $technical_proposal->update([
                    'status_id' => $proposal_status->id
                ]);

                $oferente->update([
                    'etapa_actual' => $etapa,
                    'rechazado' => $rejected,
                    'analisis_tecnica_valores' => json_encode($calification)
                ]);
                $oferente->refresh();

                // Email send
                $title = 'Resultado Calificación Técnica';
                $subject = $concurso->nombre . ' - ' . $title;
                $template = rootPath(config('app.templates_path')) . '/email/technical-evaluation.tpl';
                $users = User::where('offerer_company_id', $oferente->id_offerer)->pluck('email');
                if ($accepted) {
                    $html = $this->fetch($template, [
                        'title' => $title,
                        'ano' => Carbon::now()->format('Y'),
                        'concurso' => $concurso,
                        'accepted' => $accepted,
                        'company_name' => $oferente->company->business_name,
                        'comentario' => ''
                    ]);
                } else {
                    $html = $this->fetch($template, [
                        'title' => $title,
                        'ano' => Carbon::now()->format('Y'),
                        'concurso' => $concurso,
                        'accepted' => $accepted,
                        'company_name' => $oferente->company->business_name,
                        'comentario' => $body->comentario
                    ]);
                }



                $result = $emailService->send($html, $subject, $users, "");
                //$result['success'] = true;

                if (!$result['success']) {
                    $error = true;
                    $status = 422;
                    $message = $result['message'];
                }
            }

            if (!$error) {
                $success = true;
                $message = 'Evaluación enviada con éxito.';
                $connection->commit();

            } else {
                $success = false;
                $connection->rollBack();
            }

        } catch (\Exception $e) {
            $connection->rollBack();
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message
        ], $status);
    }

    private function updateDocuments($concurso, $oferente, $body)
    {
        $success = false;
        $message = null;
        $status = 200;

        try {
            $absolute_path = filePath($oferente->file_path, true);
            // GO
            if ($concurso->is_go) {

                $documents = collect();
                $documentsAdditional = collect();
                $documents = $documents->merge($body->DriverNoGcgDocuments);
                $documentsAdditional = $documentsAdditional->merge($body->AdditionalDriverDocuments);
                $documentsAdditional = $documentsAdditional->merge($body->AdditionalVehicleDocuments);

                $validator = $this->validateGoDocuments($body, $concurso, [
                    'go_documents' => $documents->map(
                        function ($item) {
                            return (array) $item;
                        }
                    )->toArray(),
                    'go_additional_documents' => $documents->map(
                        function ($item) {
                            return (array) $item;
                        }
                    )->toArray()
                ]);

                if ($validator->fails()) {
                    $status = 422;
                    $message = $validator->errors()->first();
                    $success = false;
                } else {
                    foreach ($documents as $document) {
                        switch ($document->action) {
                            case 'upload':
                                // Si había un archivo previo, lo eliminamos.
                                if ($document->id) {
                                    $to_delete = ParticipanteGoDocument::find($document->id);
                                    @unlink($absolute_path . DIRECTORY_SEPARATOR . $to_delete->filename);
                                    $to_delete->delete();
                                }

                                // Guardamos el nuevo archivo
                                $new_document = new ParticipanteGoDocument([
                                    'participante_id' => $oferente->id,
                                    'id_go_document' => (int) $document->document_id,
                                    'filename' => $document->filename
                                ]);

                                $new_document->save();
                                break;
                            case 'clear':
                            case 'delete':
                                // Si el archivo ya estaba guardado
                                if ($document->id) {
                                    $to_delete = ParticipanteGoDocument::find($document->id);
                                    @unlink($absolute_path . DIRECTORY_SEPARATOR . $to_delete->filename);
                                    $to_delete->delete();
                                }
                            default:
                                //continue;
                                break;
                        }
                    }

                    foreach ($documentsAdditional as $document) {
                        switch ($document->action) {
                            case 'upload':
                                // Si había un archivo previo, lo eliminamos.
                                if ($document->id) {
                                    $to_delete = ParticipanteGoDocument::find($document->id);
                                    @unlink($absolute_path . DIRECTORY_SEPARATOR . $to_delete->filename);
                                    $to_delete->delete();
                                }

                                // Guardamos el nuevo archivo
                                $new_document = new ParticipanteGoDocument([
                                    'participante_id' => $oferente->id,
                                    'id_go_document_additional' => (int) $document->document_id,
                                    'filename' => $document->filename
                                ]);

                                $new_document->save();
                                break;
                            case 'clear':
                            case 'delete':
                                // Si el archivo ya estaba guardado
                                if ($document->id) {
                                    $to_delete = ParticipanteGoDocument::find($document->id);
                                    @unlink($absolute_path . DIRECTORY_SEPARATOR . $to_delete->filename);
                                    $to_delete->delete();
                                }
                            default:
                                //continue;
                                break;
                        }
                    }

                    $success = true;
                }
                // NO-GO
            } else {
                $documents = collect();
                $documents = $documents->merge($body->documents);

                $validator = $this->validateDocuments($body, $concurso, [
                    'technical_documents' => $documents->map(
                        function ($item) {
                            return (array) $item;
                        }
                    )->toArray()
                ]);

                if ($validator->fails()) {
                    $status = 422;
                    $message = $validator->errors()->first();
                    $success = false;
                } else {
                    foreach ($documents as $document) {
                        switch ($document->action) {
                            case 'upload':
                                // Si había un archivo previo, lo eliminamos.
                                if ($document->id) {
                                    $to_delete = ProposalDocument::find($document->id);
                                    @unlink($absolute_path . DIRECTORY_SEPARATOR . $to_delete->filename);
                                    $to_delete->delete();
                                }

                                // Guardamos el nuevo archivo
                                $new_document = new ProposalDocument([
                                    'proposal_id' => $oferente->technical_proposal->id,
                                    'type_id' => (int) $document->type_id,
                                    'filename' => $document->filename
                                ]);

                                $new_document->save();
                                break;
                            case 'clear':
                            case 'delete':
                                // Si el archivo ya estaba guardado
                                if ($document->id) {
                                    $to_delete = ProposalDocument::find($document->id);
                                    @unlink($absolute_path . DIRECTORY_SEPARATOR . $to_delete->filename);
                                    $to_delete->delete();
                                }
                            default:
                                //continue;
                                break;
                        }
                    }

                    $success = true;
                }
            }

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return [
            'success' => $success,
            'message' => $message,
            'status' => $status
        ];
    }

    private function validate($body, $fields)
    {
        $conditional_rules = [];
        $common_rules = [
            'comment' => 'string|max:5000|nullable',
        ];

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules)
        );
    }

    private function validateDocuments($body, $concurso, $fields)
    { //Agregra la validaciond e los documentos tambien aca (copiar los ya hehcos) TICK



        $conditional_rules = [];
        $common_rules = [
            'technical_documents.0.filename' => 'required'
        ];


        if ($concurso->diagrama_gant === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.1.filename' => 'required'
            ]);
        }

        

        if ($concurso->seguro_caucion === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.2.filename' => 'required'
            ]);
        }

        if ($concurso->base_condiciones_firmado === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.3.filename' => 'required'
            ]);
        }

        if ($concurso->condiciones_generales === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.4.filename' => 'required'
            ]);
        }

        if ($concurso->pliego_tecnico === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.5.filename' => 'required'
            ]);
        }

        if ($concurso->acuerdo_confidencialidad === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.6.filename' => 'required'
            ]);
        }

        if ($concurso->legajo_impositivo === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.7.filename' => 'required'
            ]);
        }

        if ($concurso->antecendentes_referencia === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.8.filename' => 'required'
            ]);
        }

        if ($concurso->reporte_accidentes === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.9.filename' => 'required'
            ]);
        }
        if ($concurso->envio_muestra === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.10.filename' => 'required'
            ]);
        }
        if ($concurso->nom251 === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.11.filename' => 'required'
            ]);
        }
        if ($concurso->distintivo === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.12.filename' => 'required'
            ]);
        }
        if ($concurso->filtros_sanitarios === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.13.filename' => 'required'
            ]);
        }
        if ($concurso->repse === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.14.filename' => 'required'
            ]);
        }
        if ($concurso->poliza === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.15.filename' => 'required'
            ]);
        }
        if ($concurso->primariesgo === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.16.filename' => 'required'
            ]);
        }
        if ($concurso->obras_referencias === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.17.filename' => 'required'
            ]);
        }
        if ($concurso->obras_organigrama === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.18.filename' => 'required'
            ]);
        }
        if ($concurso->obras_equipos === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.19.filename' => 'required'
            ]);
        }
        if ($concurso->obras_cronograma === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.20.filename' => 'required'
            ]);
        }
        if ($concurso->obras_memoria === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.21.filename' => 'required'
            ]);
        }
        if ($concurso->obras_antecedentes === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.22.filename' => 'required'
            ]);
        }
        if ($concurso->tarima_ficha_tecnica === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.23.filename' => 'required'
            ]);
        }
        if ($concurso->tarima_licencia === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.24.filename' => 'required'
            ]);
        }
        if ($concurso->tarima_nom_144 === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.25.filename' => 'required'
            ]);
        }
        if ($concurso->tarima_acreditacion === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.26.filename' => 'required'
            ]);
        }
        
        if ($concurso->edificio_balance === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.27.filename' => 'required'
            ]);
        }
        if ($concurso->edificio_iva === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.28.filename' => 'required'
            ]);
        }
        if ($concurso->edificio_cuit === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.29.filename' => 'required'
            ]);
        }
        if ($concurso->edificio_brochure === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.30.filename' => 'required'
            ]);
        }
        if ($concurso->edificio_organigrama === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.31.filename' => 'required'
            ]);
        }
        if ($concurso->edificio_organigrama_obra === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.32.filename' => 'required'
            ]);
        }
        if ($concurso->edificio_subcontratistas === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.33.filename' => 'required'
            ]);
        }
        if ($concurso->edificio_gestion === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.34.filename' => 'required'
            ]);
        }
        if ($concurso->edificio_maquinas === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.35.filename' => 'required'
            ]);
        }
        if ($concurso->lista_prov === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.36.filename' => 'required'
            ]);
        }
        if ($concurso->cert_visita === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.37.filename' => 'required'
            ]);
        }



               // if ($concurso->entrega_doc_evaluacion === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.38.filename' => 'required'
        //     ]);
        // }

        // if ($concurso->requisitos_legales === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.39.filename' => 'required'
        //     ]);
        // }

        // if ($concurso->experiencia_y_referencias === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.40.filename' => 'required'
        //     ]);
        // }

        // if ($concurso->repse_two === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.41.filename' => 'required'
        //     ]);
        // }

        // if ($concurso->alcance_two === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.42.filename' => 'required'
        //     ]);
        // }

        // if ($concurso->forma_pago === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.43.filename' => 'required'
        //     ]);
        // }

        // if ($concurso->tiempo_fabricacion === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.44.filename' => 'required'
        //     ]);
        // }

        // if ($concurso->ficha_tecnica === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.45.filename' => 'required'
        //     ]);
        // }

        // if ($concurso->garantias === 'si') {
        //     $conditional_rules = array_merge($conditional_rules, [
        //         'technical_documents.46.filename' => 'required'
        //     ]);
        // }

        

        /*if ($concurso->lista_prov === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.*.filename' => [
                    'required_if:technical_documents.*.type_id,39',
                    function ($attribute, $value, $fail) use ($fields) {
                        $documentExists = collect($fields['technical_documents'])->contains(function ($document) {
                            return $document['type_id'] == 39 && !empty($document['filename']);
                        });
                        if (!$documentExists) {
                            $fail('La Lista de Sub Contratistas es obligatoria.');
                        }
                    },
                ],
            ]);
        }
    
        if ($concurso->cert_visita === 'si') {
            $conditional_rules = array_merge($conditional_rules, [
                'technical_documents.*.filename' => [
                    'required_if:technical_documents.*.type_id,40',
                    function ($attribute, $value, $fail) use ($fields) {
                        $documentExists = collect($fields['technical_documents'])->contains(function ($document) {
                            return $document['type_id'] == 40 && !empty($document['filename']);
                        });
                        if (!$documentExists) {
                            $fail('El Certificado de Visita es obligatorio.');
                        }
                    },
                ],
            ]);
        }*/

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules),
            $messages = [
                'technical_documents.0.filename.required' => 'Debe cargar una Propuesta Técnica.',
                'technical_documents.1.filename.required' => 'El Diagrama de Gantt es obligatorio.',
                'technical_documents.2.filename.required' => 'El Seguro de Caución es obligatorio.',
                'technical_documents.3.filename.required' => 'Las Bases y condiciones Firmado son obligatorias.',
                'technical_documents.4.filename.required' => 'Las Condiciones Generales Firmado son obligatorias.',
                'technical_documents.5.filename.required' => 'El Pliego Técnico Firmado es obligatorio.',
                'technical_documents.6.filename.required' => 'El Acuerdo de Confidencialidad Firmado es obligatorio.',
                'technical_documents.7.filename.required' => 'El Legajo Impositivo es obligatorio.',
                'technical_documents.8.filename.required' => 'Los Antecedentes y Referencias son obligatorios.',
                'technical_documents.9.filename.required' => 'El Reporte Accidentes es obligatorio.',
                'technical_documents.10.filename.required' => 'El Envio de muestra es obligatorio.',
                'technical_documents.11.filename.required' => 'El NOM-251-SSA1-2009 es obligatorio.',
                'technical_documents.12.filename.required' => 'El Distintivo H es obligatorio.',
                'technical_documents.13.filename.required' => 'Los Filtros Sanitarios trimestrales a los empleados son obligatorios.',
                'technical_documents.14.filename.required' => 'La documentacion REPSE es obligatoria.',
                'technical_documents.15.filename.required' => 'La Póliza de seguro responsabilidad civil es obligatoria.',
                'technical_documents.16.filename.required' => 'El Prima de Riesgo es obligatoria.',
                'technical_documents.17.filename.required' => 'Las Referencias comerciales  son obligatorias.',
                'technical_documents.18.filename.required' => 'El Organigrama de Obras es obligatorio.',
                'technical_documents.19.filename.required' => 'El Documento de Equipos y herramientas es obligatorio.',
                'technical_documents.20.filename.required' => 'El Cronograma de Obras es obligatorio.',
                'technical_documents.21.filename.required' => 'La Memoria tecnica es obligatoria.',
                'technical_documents.22.filename.required' => 'Los Antecedentes de Obras similares son obligatorios.',
                'technical_documents.23.filename.required' => 'La Ficha Técnica de Tarima es obligatoria.',
                'technical_documents.24.filename.required' => 'La Licencia ambiental integral (LAI) es obligatoria.',
                'technical_documents.25.filename.required' => 'El NOM-144 de Tarima es obligatorio.',
                'technical_documents.26.filename.required' => 'La Acreditación de Tarima es obligatoria.',
                'technical_documents.27.filename.required' => 'El Balance del Edificio es obligatorio.',
                'technical_documents.28.filename.required' => 'El IVA del Edificio es obligatorio.',
                'technical_documents.29.filename.required' => 'El CUIT del Edificio es obligatorio.',
                'technical_documents.30.filename.required' => 'El Brochure del Edificio es obligatorio.',
                'technical_documents.31.filename.required' => 'El Organigrama del Edificio es obligatorio.',
                'technical_documents.32.filename.required' => 'El Organigrama de Obras del Edificio es obligatorio.',
                'technical_documents.33.filename.required' => 'Los Subcontratistas del Edificio son obligatorios.',
                'technical_documents.34.filename.required' => 'La Gestión del Edificio es obligatoria.',
                'technical_documents.35.filename.required' => 'Las Máquinas del Edificio son obligatorias.',
                'technical_documents.36.filename.required' => 'La Lista de Sub Contratistas es obligatoria.',
                'technical_documents.37.filename.required' => 'El Certificado de Visita es obligatorio.',
                
                'technical_documents.38.filename.required' => 'El Documento Evaluacion es obligatoria.',

                'technical_documents.39.filename.required' => 'Los Requisitos Legales son obligatorios',
                'technical_documents.40.filename.required' => 'La Experiencia y referencias es obligatoria',
                'technical_documents.41.filename.required' => 'La Documentación REPSE es obligatoria',

                'technical_documents.42.filename.required' => 'Alcance es obligatorio',
                'technical_documents.43.filename.required' => 'La Forma de Pago es obligatoria',
               // 'technical_documents.44.filename.required' => 'El Tiempo de Fabricación es obligatorio ',
                'technical_documents.45.filename.required' => 'La Ficha Técnica es obligatorio ',
               // 'technical_documents.46.filename.required' => 'Garantias es obligatorio '



        
            ]
        );
    }

    private function validateGoDocuments($body, $concurso, $fields)
    {
        $conditional_rules = [];
        $common_rules = [];

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules)
        );
    }

    private function validateCalification($body, $concurso, $fields)
    {
        $conditional_rules = [];
        $common_rules = [
            'calification.*' => [
                function ($attribute, $value, $fail1) {
                    if ($value['ponderacion'] > 0) {
                        if ($value['value'] < 0 || $value['value'] > 100) {
                            $fail1('Los valores de todos los items con ponderación deben ser valores comprendidos entre 0 y 100.');
                        }
                    }
                }
            ]
        ];

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules)
        );
    }

    public function getEmailUser($user)
    {
        return User::find($user)->email;
    }

    public function newRound(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $error = false;
        try {
            $body = json_decode($request->getParsedBody()['Data']);
            $comentarioRonda = $body->reason;
            $fieldValidator = validator(
                [
                    'comment' => $comentarioRonda
                ],
                [
                    'comment' => 'required|string|max:5000',
                ],
                [
                    'comment.required' => 'Debe propociar un comentario',
                    'comment.max' => 'El maximo de caracteres es de 5000',
                ]
            );
            if ($fieldValidator->fails()) {
                $error = true;
                $status = 422;
                $message = $fieldValidator->errors()->first();
            } else {
                $connection = dependency('db')->getConnection();
                $connection->beginTransaction();
                $concurso = Concurso::find($body->IdConcurso);
                $proveedor = $concurso->oferentes->find($body->proveedor);
                $proposal = $proveedor->proposals->find($body->proposal);
                $rondaTechNueva = $proveedor->ronda_tecnica < 5 ? $proveedor->ronda_tecnica + 1 : $proveedor->ronda_tecnica;
                $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['revisada'])->first();
                if (!$proposal_status) {
                    throw new Exception('No se encontró un estado de propuesta con el código "revisada".');
                }
                $etapa = Participante::ETAPAS['tecnica-pendiente-' . $rondaTechNueva];

                $proposal->update([
                    'status_id' => $proposal_status->id,
                    'comentario_nueva_ronda' => $comentarioRonda
                ]);
                $proveedor->update([
                    'etapa_actual' => $etapa,
                    'ronda_tecnica' => $rondaTechNueva
                ]);

                $proveedor->refresh();
                
                $emailService = new EmailService();

                // Email send
                $title = 'Resultado Calificación Técnica';
                $subject = $concurso->nombre . ' - ' . $title;
                $template = rootPath(config('app.templates_path')) . '/email/technical-new-round.tpl';
                $users = User::where('offerer_company_id', $proveedor->id_offerer)->pluck('email');
                
                $html = $this->fetch($template, [
                    'title' => $title,
                    'ano' => Carbon::now()->format('Y'),
                    'concurso' => $concurso,
                    'company_name' => $proveedor->company->business_name,
                    'comentario' => $comentarioRonda,
                    'nuevaRonda' => Participante::RONDAS[$rondaTechNueva].' Técnica'
                ]);
                
                $result = $emailService->send($html, $subject, $users, "");
                
                if (!$result['success']) {
                    $error = true;
                    $status = 422;
                    $message = $result['message'];
                    $connection->rollBack();
                }

                if ($result['success']) {
                    $success = true;
                    $message = Participante::RONDAS[$rondaTechNueva].' Técnica enviada con éxito.';
                    $connection->commit();
                }
            }
        } catch (Exception $e) {
            $connection->rollBack();
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message
        ], $status);
    }
}