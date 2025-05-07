<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Concurso;
use App\Models\Evaluacion;
use App\Services\EmailService;
use Carbon\Carbon;

class EvaluationController extends BaseController
{
    public function store(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $redirect_url = null;

        try {
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();
            $emailService = new EmailService();

            $body = $request->getParsedBody();
            $evaluations = isset($body['Evaluacion']) ? $body['Evaluacion'] : [];
            
            // Depuración de la variable $evaluations
            $txt = fopen("evaluations_debug.txt", "w");
            fwrite($txt, print_r($evaluations, true)); // Cambié frwite a fwrite
            fclose($txt);
            
            $concurso = Concurso::find($body['Entity']['Id']);


            foreach ($evaluations as $sent_evaluation) {
                $idOferente = abs(intval($sent_evaluation['Id']));
                $oferente = $concurso->oferentes->where('id_offerer', '=', $idOferente)->first();
                $users = $oferente->company->users->pluck('email');
                
                if ($oferente->evaluacion) {
                    continue;
                }

                if (
                    empty($sent_evaluation['Puntualidad']) &&
                    empty($sent_evaluation['Calidad']) &&
                    empty($sent_evaluation['OrdenYlimpieza']) &&
                    empty($sent_evaluation['MedioAmbiente']) &&
                    empty($sent_evaluation['HigieneYseguridad']) &&
                    empty($sent_evaluation['Experiencia']) &&
                    empty($sent_evaluation['Comentario'])
                ) {
                    continue;
                }

                $fields = [
                    'id_participante'   => $oferente->id,
                    'etapa_actual'      => $oferente->etapa_actual,
                    'comentario'        => is_string($sent_evaluation['Comentario']) ? $sent_evaluation['Comentario'] : '',
                    'valores'           => [
                        'Puntualidad'       => $sent_evaluation['Puntualidad'],
                        'Calidad'           => $sent_evaluation['Calidad'],
                        'OrdenYlimpieza'    => $sent_evaluation['OrdenYlimpieza'],
                        'MedioAmbiente'     => $sent_evaluation['MedioAmbiente'],
                        'HigieneYseguridad' => $sent_evaluation['HigieneYseguridad'],
                        'Experiencia'       => $sent_evaluation['Experiencia'],
                    ]
                ];

                $validator = $this->validate($body, $fields);

                if ($validator->fails()) {
                    $success = false;
                    $message = $validator->errors()->first();
                    $status = 422;
                    break;
                } else {
                    $fields['valores'] = json_encode($fields['valores']);
                    $evaluation = new Evaluacion($fields);
                    $evaluation->save();
                    $evaluation->refresh();
    
                    $oferente->id_evaluacion = $evaluation->id;
                    $oferente->save();

                    $title = 'Calificación Reputación';
                    $subject = $concurso->nombre . ' - ' . $title;
        
                    $template = rootPath(config('app.templates_path')) . '/email/evaluation.tpl';
    
                    $html = $this->fetch($template, [
                        'title'         => $title,
                        'ano'           => Carbon::now()->format('Y'),
                        'concurso'      => $concurso,
                        'evaluation'    => $sent_evaluation,
                        'values'        => Evaluacion::VALUES,
                        'company_name'  => $oferente->company->business_name
                    ]);
    
                    $result = $emailService->send($html, $subject, $users, "");
                    if (!$result['success']) {
                        $message = 'Ha ocurrido un error al intentar enviar los correos.';
                        $status = 500;
                        $success = false;
                        break;
                    }
    
                    $success = true;
                }
            }

            if ($success) {
                $connection->commit();
                $message = 'Evaluaciones enviadas con éxito.';
            } else {
                $connection->rollBack();

                if (!$message) {
                    $message = 'No ha evaluado a ningún oferente.';
                    $status = 422;
                }
            }
            
        } catch (\Exception $e) {
            dd($e);
            $connection->rollBack();
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'  => [
                'redirect' => $redirect_url
            ]
        ], $status);
    }

    private function validate($body, $fields)
    {
        $conditional_rules = [];
        $common_rules = [
            'comentario'                => 'string|max:255|nullable',
            'valores.Puntualidad'       => 'required',
            'valores.Calidad'           => 'required',
            'valores.OrdenYlimpieza'    => 'required',
            'valores.MedioAmbiente'     => 'required',
            'valores.HigieneYseguridad' => 'required',
            'valores.Experiencia'       => 'required'
        ];

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules)
        );
    }
}
