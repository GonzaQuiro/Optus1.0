<?php

namespace App\Http\Controllers\Adjudication;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use Carbon\Carbon;
use App\Services\EmailService;
use App\Models\Concurso;
use App\Models\Step;
use App\Models\Participante;
use App\Models\Producto;
use App\Models\ProposalStatus;

class AdjudicationController extends BaseController
{
    public function acceptOrDecline(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $result = [];

        try {
            $body = json_decode($request->getParsedBody()['Entity']);
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();
            $emailService = new EmailService();

            $concurso = Concurso::find(intval($body->IdConcurso));
            $oferente = $concurso->oferentes->where('id_offerer', user()->offerer_company_id)->first();

            switch ($body->Action) {
                case 'accept':
                    $template = rootPath(config('app.templates_path')) . '/email/client-adjudication-accepted.tpl';
                    $title = 'Adjudicación aceptada';
                    $message = 'Adjudicación aceptada con éxito.';
                    $accept = true;
                    $etapa_actual = Participante::ETAPAS['adjudicacion-aceptada'];
                    break;
                case 'decline':
                    $template = rootPath(config('app.templates_path')) . '/email/client-adjudication-rejected.tpl';
                    $title = 'Adjudicación rechazada';
                    $message = 'Adjudicación rechazada con éxito.';
                    $accept = false;
                    $etapa_actual = Participante::ETAPAS['adjudicacion-rechazada'];

                    $concurso->update([
                        'adjudicado' => 0
                    ]);

                    $oferentes = $concurso->oferentes
                        ->where('id_offerer', '<>', user()->offerer_company_id)
                        ->where('etapa_actual', Participante::ETAPAS['economica-presentada'])
                        ->all();

                    foreach ($oferentes as $key => $value) {
                        $value->update([
                            'rechazado' => 0
                        ]);
                    }
                    break;
            }

            $oferente->update([
                'acepta_adjudicacion' => $accept,
                'acepta_adjudicacion_fecha' => Carbon::now()->format('Y-m-d'),
                'etapa_actual' => $etapa_actual
            ]);

            $connection->commit();

            $subject = $concurso->nombre . ' - ' . $title;
            $html = $this->fetch($template, [
                'title' => $title,
                'ano' => Carbon::now()->format('Y'),
                'concurso' => $concurso,
                'oferente' => $oferente,
            ]);
            
            $result = $emailService->send(
                $html,
                $subject,
                [$concurso->cliente->email],
                $concurso->cliente->full_name
            );
            //if (!$result['success']) {
            //    $error = true;
            //}
            $success = true;

        } catch (\Exception $e) {
            $connection->rollback();
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'redirect' => route('concursos.oferente.serveDetail', [
                    'id' => $concurso->id,
                    'type' => Concurso::TYPES[$concurso->tipo_concurso],
                    'step' => Step::STEPS['offerer']['adjudicado']
                ])
            ]
        ], $status);
    }

    public function send(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $redirect_url = null;
        $error = false;

        try {
            $body = json_decode($request->getParsedBody()['Data']);
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();
            $type = $body->Type;

            $concurso = Concurso::find($body->IdConcurso);
            $rondaActual = $concurso->ronda_actual;
            $adjudicatedOffererIds = [];
            $losers = collect();
            $offersByOfferer = collect();

            $common_fields = [
                'comment' => $body->Comment
            ];

            $subject = $concurso->nombre;


            if ($type == 'integral') {
                $adjudicacion = 1;
                $oferenteId = (int) $body->Data;

                $validation = $this->validate($common_fields, $type);
                if ($validation->fails()) {
                    $message = $validation->errors()->first();
                    $status = 422;
                    $error = true;
                } else {
                    $oferente = $this->getOfferers($type, $concurso, $oferenteId);

                    $proposal = $oferente->economic_proposal->where('participante_id', $oferente->id)->where('numero_ronda', $rondaActual)->where('type_id', 2)->first();
                    

                    $productsOfferer = $concurso->productos->map(
                        function ($product) use ($proposal) {
                            $oferta = array_values(
                                array_filter(
                                    $proposal->values,
                                    function ($item) use ($product) {
                                        return $item['producto'] == $product->id;
                                    }
                                )
                            )[0];
                            $oferta['cotizacion'] = $oferta['total'];
                            return $oferta;
                        }
                    );

                    $adjudicationResult = [];
                    $adjudicated_products = collect();
                    $cot_tot = 0; // Variable para acumular el total de cotización
                    foreach ($productsOfferer as $oferta) {
                        $producto = Producto::find((int) $oferta['producto']);
                        array_push($adjudicationResult, [
                            'itemId' => $producto->id,
                            'itemSolicitado' => $producto->cantidad,
                            'itemNombre' => $producto->nombre,
                            'oferenteId' => $oferente->id_offerer,
                            'cantidad' => $oferta['cantidad'],
                            'cotUnitaria' => $oferta['cotUnitaria'],
                            'cotizacion' => $oferta['cotizacion'],
                            'cantidadCot' => $oferta['cantidadCot'],
                            'cantidadAdj' => $oferta['cantidadCot'],
                            'total' => $oferta['total'],
                            'moneda' => $concurso->tipo_moneda->nombre,
                            'unidad' => $producto->unidad_medida->name,
                            'fecha' => $oferta['fecha'],
                        ]);
                        $cot_tot += $oferta['total']; // Acumulamos el valor de "total"
                    }
                    $adjudicated_products = collect($adjudicationResult);
                    $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['accepted'])->first();
                    $proposal->update([
                        'status_id' => $proposal_status->id
                    ]);
                    
                    
                    $oferente->update([
                        'etapa_actual' => Participante::ETAPAS['adjudicacion-pendiente'],
                        'adjudicacion' => $adjudicacion
                    ]);
                    array_push($adjudicatedOffererIds, $oferente->id);
                }
            }

            if ($type == 'individual') {
                $adjudicacion = 2;
                
                $stringified_items = explode(',', $body->Data);
                $jsonArray = array();
                foreach ($stringified_items as $item) {
                    $parsed_offer = explode(':', $item);
                    $jsonArray[] = array("offerer_id" => (int) $parsed_offer[0], "product_id" => (int) $parsed_offer[1]);
                }


                $validation = $this->validate($common_fields, $type);

                if ($validation->fails()) {
                    $message = $validation->errors()->first();
                    $status = 422;
                    $error = true;
                } else {
                    $adjudicated_products = collect();
                    $adjudicationResult = [];
                    $adjudicatedOffererIds = [];
                    foreach ($jsonArray as $item) {
                        array_push($adjudicatedOffererIds, $item['offerer_id']);
                        $oferente = $concurso->oferentes->where('id_offerer', $item['offerer_id'])->first();
                        $proposal = $oferente->economic_proposal->where('participante_id', $oferente->id)->where('numero_ronda', $rondaActual)->where('type_id', 2)->first();
                        $products = $concurso->productos->where('id', $item['product_id']);
                        $productsOfferer = $products->map(
                            function ($product) use ($proposal) {
                                $oferta = array_values(
                                    array_filter(
                                        $proposal->values,
                                        function ($item) use ($product) {
                                            return $item['producto'] == $product->id;
                                        }
                                    )
                                )[0];
                                $oferta['cotizacion'] = $oferta['total'];
                                return $oferta;
                            }
                        );
                        foreach ($productsOfferer as $oferta) {
                            $producto = Producto::find((int) $oferta['producto']);
                            $producto->id_offerer = $oferente->id_offerer;
                            $cot_tot = 0; // Variable para acumular el total de cotización
                            array_push($adjudicationResult, [
                                'itemId' => $producto->id,
                                'itemSolicitado' => $producto->cantidad,
                                'itemNombre' => $producto->nombre,
                                'oferenteId' => $oferente->id_offerer,
                                'cantidad' => $oferta['cantidad'],
                                'cotUnitaria' => $oferta['cotUnitaria'],
                                'cotizacion' => $oferta['cotizacion'],
                                'cantidadCot' => $oferta['cantidadCot'],
                                'cantidadAdj' => $oferta['cantidadCot'],
                                'total' => $oferta['total'],
                                'moneda' => $concurso->tipo_moneda->nombre,
                                'unidad' => $producto->unidad_medida->name,
                                'fecha' => $oferta['fecha'],
                            ]);
                            $cot_tot += $oferta['total']; // Acumulamos el valor de "total"
                        }
                        $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['accepted'])->first();
                        $proposal->update([
                            'status_id' => $proposal_status->id
                        ]);

                        $oferente->update([
                            'etapa_actual' => Participante::ETAPAS['adjudicacion-pendiente'],
                            'adjudicacion' => $adjudicacion
                        ]);
                    }
                    $adjudicated_products = collect($adjudicationResult);
                    $oferentes = $this->getOfferers($type, $concurso, $adjudicatedOffererIds);
                }

            }

            if ($type == 'manual') {
                $adjudicacion = 3;
                $manual_adjudication = $body->Data;
                

                $itemsTemp = json_decode($request->getParsedBody()['Data'], true)['Data']['items'];

                $items = array_filter($itemsTemp, function ($v, $k) {
                    return $v['quantity'] > 0;
                }, ARRAY_FILTER_USE_BOTH);
                $fields = array_merge($common_fields, [
                    'items' => $items
                ]);

                $validation = $this->validate($fields, $type);

                if ($validation->fails()) {
                    $message = $validation->errors()->first();
                    $status = 422;
                    $error = true;
                } else {
                    $id_productos = [];
                    foreach ($manual_adjudication->items as $manual_item) {
                        array_push($adjudicatedOffererIds, $concurso->oferentes->where('id', $manual_item->offerer_id)->pluck('id_offerer')->first());
                    }
                    $itemsAdjudicated = collect($items);
                    $adjudicated_products = collect();
                    $adjudicationResult = [];
                    $oferentes = $this->getOfferers($type, $concurso, $adjudicatedOffererIds);

                    foreach ($oferentes as $key => $oferente) {
                        $proposal = $oferente->economic_proposal->where('participante_id', $oferente->id)->where('numero_ronda', $rondaActual)->where('type_id', 2)->first();
                        $productsAdj = $itemsAdjudicated->where('offerer_id', $oferente->id)->pluck('product_id')->all();
                        $products = $concurso->productos->whereIn('id', $productsAdj);

                        $productsOfferer = $products->map(
                            function ($product) use ($proposal, $itemsAdjudicated) {
                                $oferta = array_values(
                                    array_filter(
                                        $proposal->values,
                                        function ($item) use ($product) {
                                            return $item['producto'] == $product->id;
                                        }
                                    )
                                )[0];
                                $oferta['cotizacion'] = $oferta['total'];
                                return $oferta;
                            }
                        );



                        foreach ($productsOfferer as $oferta) {
                            $producto = Producto::find((int) $oferta['producto']);
                            $prodAdj = $itemsAdjudicated->where('product_id', $producto->id)->where('offerer_id', $oferente->id)->first();
                            $cot_tot = 0; // Variable para acumular el total de cotización
                            array_push($adjudicationResult, [
                                'itemId' => $producto->id,
                                'itemSolicitado' => $producto->cantidad,
                                'itemNombre' => $producto->nombre,
                                'oferenteId' => $oferente->id_offerer,
                                'cantidad' => $oferta['cantidad'],
                                'cotUnitaria' => $oferta['cotUnitaria'],
                                'cotizacion' => $oferta['cotizacion'],
                                'cantidadCot' => $oferta['cantidadCot'],
                                'cantidadAdj' => $prodAdj['quantity'],
                                'total' => $oferta['total'],
                                'moneda' => $concurso->tipo_moneda->nombre,
                                'unidad' => $producto->unidad_medida->name,
                                'fecha' => $oferta['fecha'],
                            ]);
                            $cot_tot += $oferta['total']; // Acumulamos el valor de "total"
                        }

                        $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['accepted'])->first();
                        $proposal->update([
                            'status_id' => $proposal_status->id
                        ]);

                        $oferente->update([
                            'etapa_actual' => Participante::ETAPAS['adjudicacion-pendiente'],
                            'adjudicacion' => $adjudicacion
                        ]);
                    }
                    $adjudicated_products = collect($adjudicationResult);
                    

                }
            }

            $listEconomicas = ['ConcursoEconomicas' => []];



            if (!$error) {
                $concurso->update([
                    'adjudicacion_items' => json_encode($adjudicationResult),
                    'total_cotizacion' => $cot_tot, // Guardamos el total de cotización
                    'adjudicacion_comentario' => $body->Comment && !empty($body->Comment) ? $body->Comment : null,
                    'adjudicado' => true
                ]);
                require rootPath() . '/app/OldServices/calculos-etapas.php';
                calcularEtapaAnalisisOfertas($listEconomicas, $concurso->id);
            }

            if (!$error) {
                // Ganadores
                if ($type == 'integral') {
                    $result = $this->sendEmailAdjudication($type, $subject, $concurso, $oferente, $adjudicated_products);
                } else {
                    // individual y manual
                    foreach ($oferentes as $oferente) {
                        $adjudicated_products_offerer = $adjudicated_products->where('oferenteId', $oferente->id_offerer);
                        $result = $this->sendEmailAdjudication($type, $subject, $concurso, $oferente, $adjudicated_products_offerer);
                        if (!$result['success']) {
                            break;
                        }
                    }
                }

            }

            if (!$error && $result['success']) {
                if($concurso->is_sobrecerrado){
                    $losers = $concurso->oferentes
                    ->where('is_economica_revisada', true)
                    ->whereNotIn('id_offerer', $adjudicatedOffererIds);
                }else{
                    $losers = $concurso->oferentes
                    ->where('is_economica_presentada', true)
                    ->whereNotIn('id_offerer', $adjudicatedOffererIds);
                }
                
                if (count($losers) > 0) {
                    foreach ($losers as $oferente) {
                        if ($oferente->has_economica_presentada) {
                            $proposal = $oferente->economic_proposal;
                            $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['rejected'])->first();
                            $proposal->update([
                                'status_id' => $proposal_status->id
                            ]);

                            $oferente->update([
                                'rechazado' => true,
                                'adjudicacion' => $adjudicacion
                            ]);
                        } else {
                            if ($oferente->has_tecnica_presentada) {
                                $proposal = $oferente->technical_proposal;
                                $proposal_status = ProposalStatus::where('code', ProposalStatus::CODES['rejected'])->first();
                                $proposal->update([
                                    'status_id' => $proposal_status->id
                                ]);
                            }

                            $oferente->update([
                                'rechazado' => true
                            ]);
                        }
                    }
                    $result = $this->sendEmailNotAdjudication($type, $subject, $concurso, $losers);
                }
            }

            if (!$error && $result['success']) {
                // Resultado del concurso
                if ($concurso->is_sobrecerrado && $concurso->aperturasobre == 'si') {
                    $result = $this->sendEmailResultContest($concurso, $listEconomicas);
                }
            }

            if (!$error && $result['success']) {
                $connection->commit();
                $success = true;
                $message = 'La adjudicación se realizó con éxito.';
                $redirect_url = route('concursos.cliente.serveList');
            } else {
                $connection->rollBack();
                $success = false;
                $message = $message ?? 'Han ocurrido errores al enviar los correos.';
            }

        } catch (\Exception $e) {
            $connection->rollback();
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

    private function sendEmailAdjudication($type, $subject, $concurso, $oferente, $adjudicated_products)
    {
        $emailService = new EmailService();
        $title = $subject . '-' . 'Resultado Calificación Económica ' . $type;
        $template = rootPath(config('app.templates_path')) . '/email/adjudication-accepted.tpl';
        //Email de adjudication asignada
        $html = $this->fetch($template, [
            'title' => $title,
            'ano' => Carbon::now()->format('Y'),
            'concurso' => $concurso,
            'company_name' => $oferente->company->business_name,
            'adjudicated_products' => $adjudicated_products
        ]);

        $result = $emailService->send(
            $html,
            $subject,
            $oferente->company->users->pluck('email'),
            $oferente->company->business_name
        );

        return $result;
    }

    private function sendEmailResultContest($concurso, $listEconomicas)
    {
        $emailService = new EmailService();
        $oferentes = $concurso->oferentes->where('has_economica_presentada', true);
        $templateComparisonPrices = rootPath(config('app.templates_path')) . '/email/adjudication-comparison-prices.tpl';
        foreach ($oferentes as $oferente) {
            $html = $this->fetch($templateComparisonPrices, [
                'title' => 'Comparativa de precios',
                'ano' => Carbon::now()->format('Y'),
                'concurso' => $concurso,
                'company_name' => $oferente->company->business_name,
                'listEconomicas' => $listEconomicas
            ]);

            $result = $emailService->send(
                $html,
                $subject = $concurso->nombre . ' - Comparativa de precios',
                $oferente->company->users->pluck('email'),
                $oferente->company->business_name
            );
            if (!$result['success']) {
                break;
            }
        }
        return $result;
    }

    private function sendEmailNotAdjudication($type, $subject, $concurso, $oferentesLoser)
    {
        $emailService = new EmailService();
        $template = rootPath(config('app.templates_path')) . '/email/adjudication-rejected.tpl';
        $title = $subject . '-' . 'Resultado Calificación Económica ' . $type;
        foreach ($oferentesLoser as $oferente) {
            $html = $this->fetch($template, [
                'title' => $title,
                'ano' => Carbon::now()->format('Y'),
                'concurso' => $concurso,
                'company_name' => $oferente->company->business_name
            ]);

            $result = $emailService->send(
                $html,
                $subject,
                $oferente->company->users->pluck('email'),
                $oferente->company->business_name
            );
            if (!$result['success']) {
                return $result;
            }

        }
        return $result;

    }

    public function getProduct(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $result = [];

        try {

            $product = Producto::find((int) $params['id']);
            $concurso = $product->concurso;
            $rondaActual = $concurso->ronda_actual;

            $offerers = [];

            foreach ($concurso->oferentes->where('has_economica_presentada', true) as $offerer) {
                $proposal = $offerer->economic_proposal->where('participante_id', $offerer->id)->where('numero_ronda', $rondaActual)->where('type_id', 2)->first();
                
                
                $oferta = array_values(
                    array_filter(
                        $proposal->values,
                        function ($item) use ($product) {
                            return $item['producto'] == $product->id;
                        }
                    )
                )[0];

                if ($oferta) {
                    $offerers[] = [
                        'id' => (string) $offerer->id,
                        'text' => $offerer->company->business_name
                    ];
                }
            }

            $result = [
                'quantity' => $product->cantidad,
                'offerers' => $offerers
            ];

            $success = true;

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'result' => $result
            ]
        ], $status);
    }

    public function getOffererByProduct(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $result = [];

        try {

            $product = Producto::find((int) $params['id']);
            $concurso = $product->concurso;
            $rondaActual = $concurso->ronda_actual;
            
            $offerer = Participante::find((int) $params['offerer_id']);

            $proposal = $offerer->economic_proposal->where('participante_id', $offerer->id)->where('numero_ronda', $rondaActual)->where('type_id', 2)->first();
            $oferta = array_values(
                array_filter(
                    $proposal->values,
                    function ($item) use ($product) {
                        return $item['producto'] == $product->id;
                    }
                )
            )[0];

            if ($oferta) {
                $result = [
                    'price' => $oferta['cotizacion'],
                    'quantity' => $oferta['cantidad']
                ];
            }

            $success = true;

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'result' => $result
            ]
        ], $status);
    }

    public function check(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;

        try {
            $body = json_decode($request->getParsedBody()['Data'], true);

            $validation = $this->validate([
                'items' => $body
            ], 'manual');

            if ($validation->fails()) {
                $success = false;
                $message = $validation->errors()->first();
            } else {
                $success = true;
            }

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message
        ], $status);
    }

    private function validate($fields, $type)
    {
        $conditional_rules = [];
        $common_rules = [
            'comment' => [
                'string',
                'max:1000',
                'nullable'
            ]
        ];

        if ($type === 'manual') {
            $conditional_rules = array_merge($conditional_rules, [
                'items' => [
                    'required'
                ],
                'items.*' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $product_quantity = (int) $value['product']['quantity'];
                        $offerer_quantity = (int) $value['offerer']['quantity'];
                        $offerer_total_quantity = (int) $value['offererTotalQuantity'];
                        $total_quantity = (int) $value['totalQuantity'];
                        $product = Producto::find((int) $value['product_id']);
                        if ($offerer_total_quantity > $offerer_quantity) {
                            $offerer = Participante::find((int) $value['offerer_id']);
                            $fail('La suma de las cantidades asignadas al Oferente "' . $offerer->user->offerer_company->business_name . '" para el Item "' . $product->nombre . '" no puede ser mayor a ' . $offerer_quantity . '.');
                        }
                        if ($total_quantity > $product_quantity) {
                            $fail('La suma de las cantidades asignadas para el Item "' . $product->nombre . '" no puede ser mayor a ' . $product_quantity . '.');
                        }
                    },
                ],
                'items.*.quantity' => [
                    'required',
                    'numeric',
                    'gt:0'
                ],
                'items.*.product_id' => [
                    'required',
                    'exists:hijos_x_concursos,id'
                ],
                'items.*.offerer_id' => [
                    'required',
                    'exists:concursos_x_oferentes,id'
                ]
            ]);
        }

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules),
            $messages = [
                'items.required' => 'Debe ingresar valores para adjudicar.'
            ],
            $customAttributes = [
                'items.*.quantity' => 'Cantidad Asignada',
                'items.*.product_id' => 'Item',
                'items.*.offerer_id' => 'Oferente'
            ]
        );
    }

    private function getOfferers($type, $concurso, $ids)
    {
        if ($type == 'integral') {
            return $concurso->oferentes->where('id_offerer', $ids)->first();
        } else {
            return $concurso->oferentes->whereIn('id_offerer', $ids);
        }
    }
}