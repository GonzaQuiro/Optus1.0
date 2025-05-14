<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Concurso;
use App\Models\Participante;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    public function serveList(Request $request, Response $response)
    {
        $type = $_SESSION["type"];
        $viewOfferer = 'dashboard/offerer/list.tpl';
        $viewCustomer = 'dashboard/customer/list.tpl';

        if ($type == "offerer") {
            return $this->render($response, $viewOfferer, [
                'page'  => 'dashboard',
                'tipo'  => 'detalle',
                'title' => 'Dashboard'
            ]);
        } else {
            return $this->render($response, $viewCustomer, [
                'page'  => 'dashboard',
                'tipo'  => 'detalle',
                'title' => 'Dashboard'
            ]);
        }
    }

    public function list(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [
            'Invitaciones' => [],
            'Consultas'    => [],
            'Tecnicas'     => [],
            'Economicas'   => [],
            'PorAdjudicar' => []
        ];
        $breadcrumbs = [];
    
        try {
            if (!isOfferer()) {
                if (isCustomer()) {
                    $id = User()->id;
                    $customercCompanyID = User()->customer_company_id;
                    /// BEGIN CLIENT ///
                    
                    //INVITACION PENDIENTE
                    $concursosInvitacionPendiente = Concurso::where([
                        ['id_cliente', '=', $id],
                        ['deleted_at', '=', null]
                    ])->whereHas('oferentes', function ($oferentes) {
                        $oferentes
                            ->where([
                                ['etapa_actual', '=', Participante::ETAPAS['invitacion-pendiente']],
                            ]);
                    })->get();
                    
                    foreach ($concursosInvitacionPendiente as $concurso) {

                        $list['Invitaciones'][] = [
                            'id'     => $concurso->id,
                            'nombre' => $concurso->nombre,
                            'fecha'  => $concurso->fecha_limite->format('Y-m-d'),
                            'class'  => 'invitacion-color',
                            'etapa'  => Participante::ETAPAS_NOMBRES['invitacion-pendiente'],
                            'tipo_concurso'  => $concurso->tipo_concurso,
                        ];
                    }
                    
                    //FINALIZA MURO CONSULTA
                    $concursosMuroDeConsultas = Concurso::where([
                        ['id_cliente', '=', $id],
                        ['deleted_at', '=', null]
                    ])->whereHas('oferentes', function ($oferentes) {
                        $oferentes
                            ->where([
                                ['etapa_actual', '!=', Participante::ETAPAS['seleccionado']]
                            ]);
                    })->get();

                    foreach ($concursosMuroDeConsultas as $concurso) {

                        $list['Consultas'][] = [
                            'id'     => $concurso->id,
                            'nombre' => $concurso->nombre,
                            'fecha'  => $concurso->finalizacion_consultas->format('Y-m-d'),
                            'class'  => 'muro-color',
                            'etapa'  => 'Finalización Muro de Consulta',
                            'tipo_concurso'  => $concurso->tipo_concurso,
                        ];
                    }


                    //TÉCNICA PENDIENTE
                    $concursosTecnicaPendiente = Concurso::where([
                        ['id_cliente', '=', $id],
                        ['deleted_at', '=', null],
                        ['ficha_tecnica_incluye', '=' ,'si']
                    ])->whereHas('oferentes', function ($oferentes) {
                        $oferentes->where('etapa_actual', Participante::ETAPAS['tecnica-pendiente'])
                                  ->where('rechazado', '=' , '0');
                    })->get();

                    foreach ($concursosTecnicaPendiente as $concurso) {
                        $list['Tecnicas'][] = [
                            'id'     => $concurso->id,
                            'nombre' => $concurso->nombre,
                            'fecha'  => $concurso->ficha_tecnica_fecha_limite->format('Y-m-d'),
                            'class'  => 'tecnica-color',
                            'etapa'  => Participante::ETAPAS_NOMBRES['tecnica-pendiente'],
                            'tipo_concurso'  => $concurso->tipo_concurso,
                        ];
                    }


                    //ECONOMICA PENDIENTE
                    $concursosEconomicaPendiente = Concurso::where([
                            ['id_cliente', $id],
                            ['deleted_at', '=', null]
                        ])
                        ->where(function ($query) {
                            $query->where('tipo_concurso', 'sobrecerrado')
                                  ->orWhere('tipo_concurso', 'online');
                        })
                        ->whereHas('oferentes', function ($oferentes) {
                            $oferentes->whereIn('etapa_actual', [
                                'economica-presentada',
                                'economica-pendiente',
                                'economica-pendiente-1',
                                'economica-pendiente-2',
                                'economica-pendiente-3',
                                'economica-pendiente-4',
                                'economica-pendiente-5'
                            ])
                            ->where('rechazado', '=', '0');
                        })
                        ->get();
                    
                    foreach ($concursosEconomicaPendiente as $concurso) {
                        $list['Economicas'][] = [
                            'id'     => $concurso->id,
                            'nombre' => $concurso->nombre,
                            'fecha'  => $concurso->is_online
                                        ? $concurso->inicio_subasta->format('Y-m-d')
                                        : $concurso->fecha_limite_economicas->format('Y-m-d'),
                            'class'  => 'economica-color',
                            'etapa'  => Participante::ETAPAS_NOMBRES['economica-pendiente'],
                            'tipo_concurso'  => $concurso->tipo_concurso,
                        ];
                    }


                    //ADJUDICACION PENDIENTE
                    $concursoAdjudicacionPendiente = Concurso::where([
                        ['id_cliente', $id],
                        ['deleted_at', '=', null]
                    ])->whereHas('oferentes', function ($oferentes) {
                        $oferentes->where('etapa_actual', Participante::ETAPAS['adjudicacion-pendiente'])
                                  ->where('rechazado', '=' , '0');
                    })->get();
    
                    foreach ($concursoAdjudicacionPendiente as $concurso) {
                        $list['PorAdjudicar'][] = [
                            'id'     => $concurso->id,
                            'nombre' => $concurso->nombre,
                            'fecha'  =>  $concurso->fecha_limite->format('Y-m-d'),
                            'class'  => 'adjudicacion-color',
                            'etapa'  => Participante::ETAPAS_NOMBRES['adjudicacion-pendiente'],
                            'tipo_concurso'  => $concurso->tipo_concurso,
                        ];
                    }
                }

            /// BEGIN OFFERER ///
            } else {

                $offererCompanyID = User()->offerer_company_id;

                //INVITACION PENDIENTE
                $concursosInvitacionPendiente = Concurso::where([
                    ['deleted_at', '=', null]
                ])
                ->whereHas('oferentes', function ($oferentes) use ($offererCompanyID){
                    $oferentes
                        ->where([
                            ['id_offerer', '=', $offererCompanyID],
                            ['etapa_actual', '=', Participante::ETAPAS['invitacion-pendiente']],
                            ['rechazado', '=', '0']
                        ]);
                })
                ->get();                

                foreach ($concursosInvitacionPendiente as $concurso) {

                    $list['Invitaciones'][] = [
                        'id'     => $concurso->id,
                        'nombre' => $concurso->nombre,
                        'fecha'  => $concurso->fecha_limite->format('Y-m-d'),
                        'class'  => 'invitacion-color',
                        'etapa'  => Participante::ETAPAS_NOMBRES['invitacion-pendiente'],
                        'tipo_concurso'  => $concurso->tipo_concurso,
                    ];
                }


                //FINALIZA MURO CONSULTA
                $concursosMuroDeConsultas = Concurso::where([
                    ['deleted_at', '=', null]
                ])
                ->whereHas('oferentes', function ($oferentes) use ($offererCompanyID){
                    $oferentes->whereNotIn('etapa_actual', Participante::ETAPAS_RECHAZADAS)
                              ->where([
                                ['id_offerer', '=', $offererCompanyID],
                                ['rechazado', '=', '0']
                              ]);
                })
                ->get();
                
                foreach ($concursosMuroDeConsultas as $concurso) {

                    $list['Consultas'][] = [
                        'id'     => $concurso->id,
                        'nombre' => $concurso->nombre,
                        'fecha'  => $concurso->finalizacion_consultas->format('Y-m-d'),
                        'class'  => 'muro-color',
                        'etapa'  => 'Finalización Muro de Consulta',
                        'tipo_concurso'  => $concurso->tipo_concurso,
                    ];
                }


                //TÉCNICA PENDIENTE
                $concursosTecnicaPendiente = Concurso::where([
                    ['deleted_at', '=', null]
                ])
                ->whereHas('oferentes', function ($oferentes) use ($offererCompanyID){
                    $oferentes
                        ->where([
                            ['id_offerer', '=', $offererCompanyID],
                            ['rechazado', '=', '0'],
                            ['etapa_actual', '=', Participante::ETAPAS['tecnica-pendiente']]
                        ]);
                })
                ->get();

                foreach ($concursosTecnicaPendiente as $concurso) {

                    $list['Tecnicas'][] = [
                        'id'     => $concurso->id,
                        'nombre' => $concurso->nombre,
                        'fecha'  => $concurso->ficha_tecnica_fecha_limite->format('Y-m-d'),
                        'class'  => 'tecnica-color',
                        'etapa'  => Participante::ETAPAS_NOMBRES['tecnica-pendiente'],
                        'tipo_concurso'  => $concurso->tipo_concurso,
                    ];
                }


                //ECONOMICA PENDIENTE
                $concursosEconomicaPendiente = Concurso::where([
                    ['deleted_at', '=', null]
                ])
                ->where(function ($query) {
                    $query->where('tipo_concurso', 'sobrecerrado')
                          ->orWhere('tipo_concurso', 'online');
                })
                ->whereHas('oferentes', function ($oferentes) use ($offererCompanyID){
                    $oferentes->whereIn('etapa_actual', [
                        'economica-presentada',
                        'economica-pendiente',
                        'economica-pendiente-1',
                        'economica-pendiente-2',
                        'economica-pendiente-3',
                        'economica-pendiente-4',
                        'economica-pendiente-5'
                    ])
                    ->where([
                        ['id_offerer', '=', $offererCompanyID],
                        ['rechazado', '=', '0']
                    ]);
                })
                ->get();

                foreach ($concursosEconomicaPendiente as $concurso) {

                    $list['Economicas'][] = [
                        'id'     => $concurso->id,
                        'nombre' => $concurso->nombre,
                        'fecha'  => $concurso->is_online 
                                    ? $concurso->inicio_subasta->format('Y-m-d H:i:s') 
                                    : $concurso->fecha_limite_economicas->format('Y-m-d H:i:s'),
                        'class'  => 'economica-color',
                        'etapa'  => Participante::ETAPAS_NOMBRES['economica-pendiente'],
                        'tipo_concurso'  => $concurso->tipo_concurso,
                    ];
                }


                //ADJUDICACION PENDIENTE
                $concursoAdjudicacionPendiente = Concurso::where([
                    ['deleted_at', '=', null]
                ])
                ->whereHas('oferentes', function ($oferentes) use ($offererCompanyID){
                    $oferentes
                        ->where([
                            ['id_offerer', '=', $offererCompanyID],
                            ['rechazado', '=', '0'],
                            ['etapa_actual', '=', Participante::ETAPAS['adjudicacion-pendiente']]
                        ]);
                })
                ->get();

                foreach ($concursoAdjudicacionPendiente as $concurso) {

                    $list['PorAdjudicar'][] = [
                        'id'     => $concurso->id,
                        'nombre' => $concurso->nombre,
                        'fecha'  => $concurso->fecha_limite->format('Y-m-d'),
                        'class'  => 'adjudicacion-color',
                        'etapa'  => Participante::ETAPAS_NOMBRES['adjudicacion-pendiente'],
                        'tipo_concurso'  => $concurso->tipo_concurso,
                    ];
                }
            }

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Dashboard', 'url' => null]
            ];
        } catch (\Exception $e) {
            dd($e);
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'list'          => $list,
                'breadcrumbs'   => $breadcrumbs
            ]
        ], $status);
    }
}
