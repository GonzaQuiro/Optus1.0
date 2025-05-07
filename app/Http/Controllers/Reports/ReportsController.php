<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\BaseController;
use App\Models\Concurso;
use App\Models\CustomerCompany;
use App\Models\OffererCompany;
use App\Models\UserType;
use Carbon\Carbon;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;

class ReportsController extends BaseController
{
    public function serveListAdj(Request $request, Response $response)
    {
        return $this->render($response, 'reportes/adjudicados/list.tpl', [
            'page' => 'reportes',
            'accion' => 'lista-concursos-adjudicados',
            'title' => 'Concursos Adjudicados'
        ]);
    }

    public function listAdj(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $user = user();
            $concursos = isAdmin() ? Concurso::where('adjudicado', true)->get() : $user->customer_company->getAllConcursosByCompany()->where('adjudicado', true)->get();
            foreach ($concursos as $concurso) {
                $type = $concurso->getAdjudicationTypeAttribute();
                array_push($list, [
                    'Id' => $concurso->id,
                    'Nombre' => $concurso->nombre,
                    'Tipo' => $type,
                    'Proveedores' => $concurso->offerers_adjudicated->implode(', '),
                    'Detalles' => $this->detailsAdj($concurso),
                ]);
            }
            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Reporte Concursos Adjudicados', 'url' => null]
            ];

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'list' => $list,
                'filtros' => $this->setFiltersAdj(),
                'breadcrumbs' => $breadcrumbs
            ]
        ], $status);
    }

    private function detailsAdj($concurso)
    {
        $details = [];
        $productos = $concurso->productos;
        $adjudication = $concurso->adjudicacion_items;
        $oferente = $concurso->oferentes->find($adjudication[0]['oferenteId']);
        foreach ($adjudication as $adj) {
            $producto = $productos->find($adj['itemId']);
            $oferente = $concurso->oferentes->find($adj['oferenteId']);
            $proposal = $oferente->economic_proposal;
            $precioUni = floatval($adj['cotUnitaria']);
            $costoObj = floatval($producto->targetcost);
            $ahorro = $costoObj != 0 ? $costoObj - $precioUni : 'N/A';
            $ahorroRel = $costoObj != 0 ? ($ahorro * 100) / $costoObj : 'N/A';
            $det = [
                'id' => $concurso->id,
                'nombre' => $concurso->nombre,
                'pos' => $adj['itemId'],
                'item' => $adj['itemNombre'],
                'cant' => $adj['cantidad'],
                'unidad' => $adj['unidad'],
                'unitario' => number_format($precioUni, 2, ',', ''),
                'costoObj' => $costoObj != 0 ? number_format($costoObj, 2, ',', '') : 'N/A',
                'ahorro' => $ahorro === 'N/A' ? $ahorro : number_format($ahorro, 2, ',', ''),
                'ahorroRel' => $ahorroRel === 'N/A' ? $ahorroRel : number_format($ahorroRel, 2, ',', ''),
                'plazoP' => $proposal->payment_deadline,
                'plazoE' => $adj['fecha'],
                'prov' => $adj['razonSocial'],
                'aceptAdj' => $oferente->acepta_adjudicacion ? 'SI' : 'NO',
                'FechaAdj' => $oferente->acepta_adjudicacion ? $oferente->acepta_adjudicacion_fecha : null,
                'tipo' => $concurso->getAdjudicationTypeAttribute(),
                'comentario' => $concurso->adjudicacion_comentario,
                'evalTech' => $concurso->technical_includes ? number_format($oferente->analisis_tecnica_valores[0]['alcanzado'], 2, ',', '') : 'N/A',
                'userAdj' => $concurso->cliente->full_name
            ];
            array_push($details, $det);
        }
        return $details;
    }
    public function setFiltersAdj()
    {
        if (isAdmin()) {
            $customers = User::select('id', 'customer_company_id', 'first_name', 'last_name')->with('customer_company.associated_offerers')->where('type_id', 3)->get();
        } else {
            $customers = User::select('id', 'customer_company_id', 'first_name', 'last_name')->with('customer_company.associated_offerers')->where('type_id', 3)->where('customer_company_id', user()->customer_company->id)->get();
        }
        $filterCustomer = [];
        foreach ($customers as $customer) {
            $offerersAso = $customer->customer_company->associated_offerers->map(function ($item, $key) {
                return ['id' => $item->id, 'text' => $item->business_name];
            });
            array_push($filterCustomer, [
                'id' => $customer->id,
                'text' => $customer->full_name,
                'offerers' => $offerersAso
            ]);
        }

        $offerers = isAdmin() ? OffererCompany::select('id', 'business_name')->get() : user()->customer_company->associated_offerers;
        $filterOfferes = $offerers->map(function ($item, $key) {
            return ['id' => $item->id, 'text' => $item->business_name];
        });
        $filters = new \StdClass();
        $filters->customers = $filterCustomer;
        $filters->offerers = $filterOfferes;
        $filters->customersSelected = [];
        $filters->offerersSelected = [];
        return $filters;
    }

    public function filterAdj(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $user = user();
            $concursos = isAdmin() ? Concurso::with('oferentes')->where('adjudicado', true) : $user->customer_company->getAllConcursosByCompany()->with('oferentes')->where('adjudicado', true);
            $filters = json_decode($request->getParsedBody()['Filters']);
            $desde = array_key_exists('Desde', $filters) ? Carbon::createFromFormat('d-m-Y', $filters->Desde)->format('Y-m-d') : null;
            $hasta = array_key_exists('Hasta', $filters) ? Carbon::createFromFormat('d-m-Y', $filters->Hasta)->format('Y-m-d') : null;
            $customersSelected = empty($filters->CompradoresSelected) ? null : $filters->CompradoresSelected;
            $offerersSelected = empty($filters->ProveedoresSelected) ? null : $filters->ProveedoresSelected;

            if (!empty($customersSelected)) {
                $concursos = $concursos->whereIn('id_cliente', $customersSelected);
            }
            $concursos = $concursos->get();

            if (!empty($offerersSelected)) {
                $concursosFiltrados = collect();
                foreach ($concursos as $key => $concurso) {
                    $offerers = $concurso->oferentes->whereIn('id_offerer', $offerersSelected)->where('adjudicacion', '>', 0);
                    if (count($offerers) > 0) {
                        $concursosFiltrados->push($concursos[$key]);
                    }
                }
                $concursos = $concursosFiltrados;

            }


            if ((!empty($desde) || !empty($hasta)) and count($concursos) > 0) {
                $concursosFechas = collect();
                foreach ($concursos as $key => $concurso) {
                    $offerers = $concurso->oferentes->whereBetween('acepta_adjudicacion_fecha', [$desde, $hasta]);
                    if (count($offerers) > 0) {
                        $concursosFechas->push($concursos[$key]);
                    }
                }
                $concursos = $concursosFechas;
            }



            if (count($concursos) > 0) {
                foreach ($concursos as $concurso) {
                    $type = $concurso->getAdjudicationTypeAttribute();
                    array_push($list, [
                        'Id' => $concurso->id,
                        'Nombre' => $concurso->nombre,
                        'Tipo' => $type,
                        'Proveedores' => $concurso->offerers_adjudicated->implode(', '),
                        'Detalles' => $this->detailsAdj($concurso),
                    ]);
                }
            }

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Reporte Concursos Adjudicados', 'url' => null]
            ];

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'list' => $list,
                'filtros' => $filters,
                'breadcrumbs' => $breadcrumbs
            ]
        ], $status);
    }

    public function serveListEval(Request $request, Response $response)
    {
        return $this->render($response, 'reportes/evaluados/list.tpl', [
            'page' => 'reportes',
            'accion' => 'lista-concursos-evaluados',
            'title' => 'Concursos Evaluados'
        ]);
    }

    public function listEval(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $user = user();
            $concursos = isAdmin() ? Concurso::where('adjudicado', true)->get() : $user->customer_company->getAllConcursosByCompany()->where('adjudicado', true)->get();
            foreach ($concursos as $concurso) {
                $concursosEval = $concurso->eval_offerers_adjudicated->implode(', ');
                $evalResult = $this->getOfferersReputation($concurso);
                if (!empty($concursosEval)) {
                    $type = $concurso->getAdjudicationTypeAttribute();
                    array_push($list, [
                        'Id' => $concurso->id,
                        'Nombre' => $concurso->nombre,
                        'Tipo' => $type,
                        'Proveedores' => $concurso->eval_offerers_adjudicated->implode(', '),
                        'Evaluadores' => $concurso->users_eval_tec->pluck('username')->implode(', '),
                        'Detalles' => $evalResult
                    ]);
                }

            }
            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Reporte Concursos Adjudicados', 'url' => null]
            ];

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'list' => $list,
                'filtros' => $this->setFiltersEval(),
                'breadcrumbs' => $breadcrumbs
            ]
        ], $status);
    }

    public function getOfferersReputation($concurso)
    {
        $return = [];
        $oferentes = $concurso->oferentes->where('is_adjudicacion_aceptada', true);

        foreach ($oferentes as $oferente) {
            $comentario = '';
            if (isset($oferente->evaluacion)) {
                if (isset($oferente->evaluacion->comentario))
                    if (is_string($oferente->evaluacion->comentario))
                        $comentario = $oferente->evaluacion->comentario;
            }

            $evaluacion = [
                'Concurso' => $concurso->id,
                'Nombre' => $concurso->nombre,
                'Id' => $oferente->id_offerer,
                'RazonSocial' => $oferente->company->business_name,
                'Puntualidad' => '',
                'Calidad' => '',
                'OrdenYlimpieza' => '',
                'MedioAmbiente' => '',
                'HigieneYseguridad' => '',
                'Experiencia' => '',
                'Comentario' => $comentario
            ];
            if ($oferente->evaluacion) {
                if (!is_null($oferente->evaluacion->valores) && !empty($oferente->evaluacion->valores)) {
                    $valores = json_decode($oferente->evaluacion->valores, true);
                    $valoresEvaluados = 0;
                    $puntaje = 0;
                    foreach ($valores as $value) {
                        if (intval($value) != 0) {
                            $valoresEvaluados++;
                            $puntaje = $puntaje + intval($value);
                        }
                    }
                    if ($valoresEvaluados > 0) {
                        $valores['puntaje'] = number_format($puntaje / $valoresEvaluados, 2, ',', '');
                    }
                    $evaluacion = array_merge($evaluacion, $valores);
                }
            }
            $return[] = $evaluacion;
        }
        return $return;
    }

    public function filterEval(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $user = user();
            $concursos = isAdmin() ? Concurso::with('oferentes')->where('adjudicado', true) : $user->customer_company->getAllConcursosByCompany()->with('oferentes')->where('adjudicado', true);
            $filters = json_decode($request->getParsedBody()['Filters']);
            $customersSelected = empty($filters->CompradoresSelected) ? null : $filters->CompradoresSelected;
            $offerersSelected = empty($filters->ProveedoresSelected) ? null : $filters->ProveedoresSelected;

            if (!empty($customersSelected)) {
                $concursos = $concursos->whereIn('id_cliente', $customersSelected);
            }
            $concursos = $concursos->get();

            if (!empty($offerersSelected)) {
                $concursosFiltrados = collect();
                foreach ($concursos as $key => $concurso) {
                    $offerers = $concurso->oferentes->whereIn('id_offerer', $offerersSelected)->where('adjudicacion', '>', 0);
                    if (count($offerers) > 0) {
                        $concursosFiltrados->push($concursos[$key]);
                    }
                }
                $concursos = $concursosFiltrados;

            }

            if (count($concursos) > 0) {
                foreach ($concursos as $concurso) {
                    $concursosEval = $concurso->eval_offerers_adjudicated->implode(', ');
                    $evalResult = $this->getOfferersReputation($concurso);
                    if (!empty($concursosEval)) {
                        $type = $concurso->getAdjudicationTypeAttribute();
                        array_push($list, [
                            'Id' => $concurso->id,
                            'Nombre' => $concurso->nombre,
                            'Tipo' => $type,
                            'Proveedores' => $concurso->eval_offerers_adjudicated->implode(', '),
                            'Evaluadores' => $concurso->users_eval_tec->pluck('username')->implode(', '),
                            'Detalles' => $evalResult
                        ]);
                    }
                }
            }

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Reporte Concursos Adjudicados', 'url' => null]
            ];

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'list' => $list,
                'filtros' => $filters,
                'breadcrumbs' => $breadcrumbs
            ]
        ], $status);
    }

    public function setFiltersEval()
    {
        if (isAdmin()) {
            $customers = User::select('id', 'customer_company_id', 'first_name', 'last_name')->with('customer_company.associated_offerers')->where('type_id', 3)->get();
        } else {
            $customers = User::select('id', 'customer_company_id', 'first_name', 'last_name')->with('customer_company.associated_offerers')->where('type_id', 3)->where('customer_company_id', user()->customer_company->id)->get();
        }
        $filterCustomer = [];
        foreach ($customers as $customer) {
            $offerersAso = $customer->customer_company->associated_offerers->map(function ($item, $key) {
                return ['id' => $item->id, 'text' => $item->business_name];
            });
            array_push($filterCustomer, [
                'id' => $customer->id,
                'text' => $customer->full_name,
                'offerers' => $offerersAso
            ]);
        }

        $offerers = isAdmin() ? OffererCompany::select('id', 'business_name')->get() : user()->customer_company->associated_offerers;
        $filterOfferes = $offerers->map(function ($item, $key) {
            return ['id' => $item->id, 'text' => $item->business_name];
        });
        $filters = new \StdClass();
        $filters->customers = $filterCustomer;
        $filters->offerers = $filterOfferes;
        $filters->customersSelected = [];
        $filters->offerersSelected = [];
        return $filters;
    }
}