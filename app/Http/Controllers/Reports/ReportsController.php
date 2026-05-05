<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\BaseController;
use App\Models\Concurso;
use App\Models\CustomerCompany;
use App\Models\OffererCompany;
use App\Models\Solped;
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
        $details      = [];
        $productos    = $concurso->productos;
        // Aseguramos que siempre sea un array
        $adjudication = $concurso->adjudicacion_items ?? [];

        foreach ($adjudication as $adj) {
            // Buscamos el producto y el oferente de forma segura
            $producto = $productos->firstWhere('id', $adj['itemId']);
            $oferente = $concurso->oferentes->firstWhere('id_offerer', $adj['oferenteId']);

            if (! $producto || ! $oferente) {
                continue;
            }

            // Precio unitario y costo objetivo
            $precioUni = floatval($adj['cotUnitaria']);
            $costoObj  = floatval($producto->targetcost);

            // Ahorro absoluto
            $ahorro = $costoObj - $precioUni;

            // Ahorro relativo: solo si costoObj > 0
            if ($costoObj > 0) {
                $ahorroRel = ($ahorro * 100) / $costoObj;
            } else {
                // Evita división por cero
                $ahorroRel = null;
            }

            // Formateos numéricos
            $unitario   = number_format($precioUni, 2, ',', '');
            $costoObjF  = $costoObj > 0 ? number_format($costoObj, 2, ',', '') : 'N/A';
            $ahorroF    = $costoObj > 0 ? number_format($ahorro,   2, ',', '') : 'N/A';
            $ahorroRelF = $costoObj > 0 ? number_format($ahorroRel, 2, ',', '') : 'N/A';

            // Propuesta económica (puede ser null)
            $proposal = $oferente->economic_proposal ?? null;
            $plazoP   = $proposal->payment_deadline ?? null;

            // Aceptación de adjudicación
            $aceptAdj = $oferente->acepta_adjudicacion ?? false;
            $fechaAdj = $aceptAdj
                ? ($oferente->acepta_adjudicacion_fecha ?? null)
                : null;

            // Evaluación técnica (solo si aplica)
            $evalTech = 'N/A';
            if (
                $concurso->technical_includes
                && is_array($oferente->analisis_tecnica_valores)
                && isset($oferente->analisis_tecnica_valores[0]['alcanzado'])
            ) {
                $evalTech = number_format(
                    $oferente->analisis_tecnica_valores[0]['alcanzado'],
                    2, ',', ''
                );
            }

            $details[] = [
                'id'         => $concurso->id,
                'nombre'     => $concurso->nombre,
                'areaSol'    => $concurso->area_sol ?? 'N/A',
                'pos'        => $adj['itemId'],
                'item'       => $adj['itemNombre'],
                'cant'       => $adj['cantidad'],
                'unidad'     => $adj['unidad'],
                'unitario'   => $unitario,
                'costoObj'   => $costoObjF,
                'ahorro'     => $ahorroF,
                'ahorroRel'  => $ahorroRelF,
                'plazoP'     => $plazoP,
                'plazoE'     => $adj['fecha'],
                'prov'       => $oferente->company->business_name ?? '',
                'aceptAdj'   => $aceptAdj ? 'SI' : 'NO',
                'FechaAdj'   => $fechaAdj,
                'tipo'       => $concurso->getAdjudicationTypeAttribute(),
                'comentario' => $concurso->adjudicacion_comentario ?? null,
                'evalTech'   => $evalTech,
                'userAdj'    => $concurso->cliente->full_name ?? null,
            ];
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

    public function serveListSolicitudes(Request $request, Response $response)
    {
        return $this->render($response, 'reportes/solicitudes/list.tpl', [
            'page' => 'reportes',
            'accion' => 'lista-solicitudes',
            'title' => 'Solicitudes'
        ]);
    }

    public function listSolicitudes(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];
        $breadcrumbs = [
            ['description' => 'Reporte Solicitudes', 'url' => null]
        ];

        try {
            $list = $this->buildSolicitudesReport();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
            $status = $status >= 100 ? $status : 500;
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'list' => $list,
                'filtros' => $this->setFiltersSolicitudes(),
                'breadcrumbs' => $breadcrumbs
            ]
        ], $status);
    }

    public function filterSolicitudes(Request $request, Response $response, $params = [])
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];
        $filters = new \StdClass();
        $breadcrumbs = [
            ['description' => 'Reporte Solicitudes', 'url' => null]
        ];

        try {
            $body = $request->getParsedBody();
            $filters = isset($body['Filters']) ? json_decode($body['Filters']) : new \StdClass();
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Filtros invalidos', 400);
            }

            $list = $this->buildSolicitudesReport($filters);
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
            $status = $status >= 100 ? $status : 500;
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

    public function setFiltersSolicitudes()
    {
        if (isAdmin()) {
            $buyers = User::select('id', 'customer_company_id', 'first_name', 'last_name')->where('type_id', 3)->get();
        } else {
            $buyers = User::select('id', 'customer_company_id', 'first_name', 'last_name')
                ->where('type_id', 3)
                ->where('customer_company_id', user()->customer_company->id)
                ->get();
        }

        $filterBuyers = $buyers->map(function ($buyer) {
            return [
                'id' => $buyer->id,
                'text' => $buyer->full_name
            ];
        })->values();

        $filters = new \StdClass();
        $filters->customers = $filterBuyers;
        $filters->customersSelected = [];
        return $filters;
    }

    private function buildSolicitudesReport($filters = null)
    {
        $query = $this->solicitudesQuery();

        $desde = $this->parseSolicitudDateFilter($this->filterValue($filters, 'Desde'), false);
        $hasta = $this->parseSolicitudDateFilter($this->filterValue($filters, 'Hasta'), true);
        $compradoresSelected = $this->filterIds($filters, 'CompradoresSelected');

        if ($desde) {
            $query->where('fecha_alta', '>=', $desde);
        }

        if ($hasta) {
            $query->where('fecha_alta', '<=', $hasta);
        }

        if (!empty($compradoresSelected)) {
            $query->whereIn('id_comprador_sugerido', $compradoresSelected);
        }

        $list = [];
        $solpeds = $query->orderBy('id', 'desc')->get();

        foreach ($solpeds as $solped) {
            $list[] = $this->mapSolicitudReport($solped);
        }

        return $list;
    }

    private function solicitudesQuery()
    {
        $relations = [
            'solicitante',
            'comprador_sugerido',
            'comprador_decision',
            'productos'
        ];

        if (isAdmin()) {
            return Solped::withTrashed()
                ->with($relations)
                ->where('estado_actual', '!=', 'borrador');
        }

        return user()->customer_company->getAllSolpedsByCompany()
            ->withTrashed()
            ->with($relations)
            ->where('estado_actual', '!=', 'borrador');
    }

    private function mapSolicitudReport($solped)
    {
        $comprador = $solped->comprador_sugerido ? $solped->comprador_sugerido->full_name : null;
        $compradorDecision = $solped->comprador_decision ? $solped->comprador_decision->full_name : null;
        $tipoCompra = $this->tipoCompraSolicitud($solped);
        $detalles = [];
        $items = $solped->productos && count($solped->productos) > 0 ? $solped->productos : collect([null]);

        foreach ($items as $item) {
            $detalles[] = [
                'id' => $this->valueOrDash($solped->id),
                'nombre' => $this->valueOrDash($solped->nombre),
                'fechaCreacion' => $this->formatSolicitudDate($solped->fecha_alta),
                'areaSolicitante' => $this->valueOrDash($solped->area_sol),
                'usuarioSolicitante' => $this->valueOrDash($solped->solicitante ? $solped->solicitante->full_name : null),
                'compradorSugerido' => $this->valueOrDash($comprador),
                'itemSolicitado' => $this->valueOrDash($item ? $item->nombre : null),
                'fechaResolucion' => $this->formatSolicitudDate($solped->fecha_resolucion),
                'fechaEntrega' => $this->formatSolicitudDate($solped->fecha_entrega),
                'tipoCompra' => $this->valueOrDash($tipoCompra),
                'estado' => $this->valueOrDash($solped->estado_actual),
                'compradorDecision' => $this->valueOrDash($compradorDecision),
                'numeroLicitacionSubasta' => '-',
                'fechaTratamiento' => $this->formatSolicitudDate($solped->fecha_inicio_licitacion)
            ];
        }

        return [
            'Id' => $solped->id,
            'Nombre' => $solped->nombre,
            'Area' => $solped->area_sol,
            'TipoCompra' => $tipoCompra,
            'CompradorSugerido' => $comprador,
            'Detalles' => $detalles
        ];
    }

    private function tipoCompraSolicitud($solped)
    {
        $tipoCompra = (string) $solped->tipo_compra;

        return isset(Solped::TYPE_DESCRIPTION[$tipoCompra]) ? Solped::TYPE_DESCRIPTION[$tipoCompra] : '';
    }

    private function formatSolicitudDate($date)
    {
        if (empty($date)) {
            return '-';
        }

        if ($date instanceof \DateTimeInterface) {
            return $date->format('d-m-Y');
        }

        return Carbon::parse($date)->format('d-m-Y');
    }

    private function valueOrDash($value)
    {
        if ($value === null) {
            return '-';
        }

        if (is_string($value) && trim($value) === '') {
            return '-';
        }

        return $value;
    }

    private function filterValue($filters, $name)
    {
        return is_object($filters) && property_exists($filters, $name) ? $filters->{$name} : null;
    }

    private function filterIds($filters, $name)
    {
        $value = $this->filterValue($filters, $name);
        if (empty($value)) {
            return [];
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        return array_values(array_filter(array_map('intval', $value)));
    }

    private function parseSolicitudDateFilter($value, $endOfDay)
    {
        if (empty($value)) {
            return null;
        }

        $date = Carbon::createFromFormat('d-m-Y', $value);
        return $endOfDay ? $date->endOfDay() : $date->startOfDay();
    }
}
