<?php

use Carbon\Carbon;
use App\Models\Concurso;
use App\Models\Producto;
use App\Models\Participante;

function calcularEtapaAnalisisOfertas(&$list, $concurso_id)
{
    $concurso = Concurso::find($concurso_id);
    $cant_productos = $concurso->productos->count();

    $rondas = $concurso->ronda_actual - 1;
    $titleRound = [
        0 => [
            'title' => '1ª Ronda de oferta',
            'active' => false,
            'ref' => 'PrimeraRonda',
            'ConcursoEconomicas' => [
                'mejoresOfertas' => [
                    'mejorIntegral' => [],
                    'mejorIndividual' => [],
                    'mejorManual' => [],
                ],
                'adjudicacion' => [],
                'proveedores' => []
            ]
        ],
        1 => [
            'title' => '2ª Ronda de oferta',
            'active' => false,
            'ref' => 'SegundaRonda',
            'ConcursoEconomicas' => [
                'mejoresOfertas' => [
                    'mejorIntegral' => [],
                    'mejorIndividual' => [],
                    'mejorManual' => [],
                ],
                'adjudicacion' => [],
                'proveedores' => []
            ]
        ],
        2 => [
            'title' => '3ª Ronda de oferta',
            'active' => false,
            'ref' => 'TerceraRonda',
            'ConcursoEconomicas' => [
                'mejoresOfertas' => [
                    'mejorIntegral' => [],
                    'mejorIndividual' => [],
                    'mejorManual' => [],
                ],
                'adjudicacion' => [],
                'proveedores' => []
            ]
        ],
        3 => [
            'title' => '4ª Ronda de oferta',
            'active' => false,
            'ref' => 'CuartaRonda',
            'ConcursoEconomicas' => [
                'mejoresOfertas' => [
                    'mejorIntegral' => [],
                    'mejorIndividual' => [],
                    'mejorManual' => [],
                ],
                'adjudicacion' => [],
                'proveedores' => []
            ]
        ],
        4 => [
            'title' => '5ª Ronda de oferta',
            'active' => false,
            'ref' => 'QuintaRonda',
            'ConcursoEconomicas' => [
                'mejoresOfertas' => [
                    'mejorIntegral' => [],
                    'mejorIndividual' => [],
                    'mejorManual' => [],
                ],
                'adjudicacion' => [],
                'proveedores' => []
            ]
        ]
    ];

    $ids = $concurso->productos->pluck('id');
    $nombres = $concurso->productos->pluck('nombre');
    $cantidades = $concurso->productos->pluck('cantidad');
    $productos = $concurso->productos;
    
    

    if ($concurso->technical_includes) {
        $oferentes = $concurso->oferentes->where('has_tecnica_aprobada');
    } else {
        $oferentes = $concurso->oferentes->where('has_invitacion_aceptada');
    }

    for ($ronda = 0; $ronda <= $rondas; $ronda++) {
        $roundTab = $titleRound[$ronda];
        $roundTab['active'] = $ronda === $rondas ? true : false;
        $oferenteItems = [];

        foreach ($oferentes as $oferente) {
            // GO: Ignoramos aquellos oferentes que no tienen documentación habilitada.
            if ($concurso->is_go && $oferentes->where('id', $oferente->id)->where('success', false)->count() > 0) {
                continue;
            }

            $oferenteItems[] = setOferente($concurso, $oferente, $ronda);
        }
        
        if (count($oferenteItems) > 0) {
            $listtotales = [];
            foreach ($oferenteItems as $g => $f) {
                $listtotales[$g] = $f['total'];
            }
            $maxtotal = min($listtotales);
            foreach ($oferenteItems as $g => $f) {
                if ($f['total'] == 0) {
                    $oferenteItems[$g]['difvsmejorofert'] = 0;
                } else {
                    $oferenteItems[$g]['difvsmejorofert'] = round((($maxtotal - $f['total']) / $f['total']) * 100, 2);
                }
            }
            
            $mejorIntegral = setMejorIntegral($concurso, $oferenteItems);
            $posicion = array_search($mejorIntegral['idOferente'], array_column($oferenteItems, 'oferente_id'));
            $oferenteItems[$posicion]['mejorOfertaIntegral'] = true;
            $mejorIndividual = setMejorIndividual($concurso, $oferenteItems);
            $oferenteItems = $mejorIndividual['oferentes'];
            $mejorManual = setMejorManual($concurso, $mejorIntegral);
            $roundTab['ConcursoEconomicas']['cantidadesSolicitadas'] = $cantidades;
            $roundTab['ConcursoEconomicas']['productos'] = $productos;
            $roundTab['ConcursoEconomicas']['mejoresOfertas']['mejorIntegral'] = $mejorIntegral;
            $roundTab['ConcursoEconomicas']['mejoresOfertas']['mejorIndividual'] = $mejorIndividual['mejorIndividual'];
            $roundTab['ConcursoEconomicas']['mejoresOfertas']['mejorManual'] = $mejorManual;
            $roundTab['ConcursoEconomicas']['proveedores'] = $oferenteItems;
        }
        $list['RondasOfertas'][$ronda] = $roundTab;
    }
}

function isMejorOferta($concurso, $total, $mejorOferta)
{
    if ($mejorOferta === 0) {
        return true;
    }

    if (
        $concurso->is_online &&
        $concurso->tipo_valor_ofertar === 'ascendente'
    ) {
        return $total > $mejorOferta;
    }

    return $total < $mejorOferta;
}

function getEconomicDoc($economic_proposal)
{
    $fileName = isset($economic_proposal->documents->first()->filename) ? $economic_proposal->documents->first()->filename : null;
    return $fileName;
}

function getCostDocument($concurso, $economic_proposal)
{
    $fileName = null;
    if ($concurso->estructura_costos === 'si') {
        $fileName = isset($economic_proposal->documents->first()->filename) ? $economic_proposal->documents->last()->filename : null;
    }
    return $fileName;
}

function getApuDocument($concurso, $economic_proposal)
{
    $fileName = null;
    if ($concurso->apu === 'si') {
        $fileName = isset($economic_proposal->documents->first()->filename) ? $economic_proposal->documents->last()->filename : null;
    }
    return $fileName;
}

function setOferente($concurso, $oferente, $ronda)
{
    // type_id 2 = a propuesta economica
    $oferenteItems = [];
    $ronda += 1;

    $evaluation = $oferente->analisis_tecnica_valores ? $oferente->analisis_tecnica_valores[0] : null;
    $evaluationalcanzada = $evaluation ? number_format($evaluation['alcanzado'], 1) : 'No Aplica';

    $values = [];
    $total = (float) 0;
    $totaltargetcost = (float) 0;
    if (!$oferente->is_concurso_rechazado) {
        $economic_proposal = $oferente->economic_proposal;
        if ($economic_proposal) {
            $economicByRound = $economic_proposal->where('participante_id', $oferente->id)->where('numero_ronda', $ronda)->where('type_id', 2)->first();
            if ($economicByRound) {
                $i = 0;

                foreach ($economicByRound->values as $propuesta) {
                    // Con esto evitamos que la lista de propuesta incluya los logs
                    $i++;
                    if ($i > $concurso->productos->count()) {
                        break;
                    }

                    $producto = Producto::find($propuesta['producto']);
                    $cotizacion = isset($propuesta['cotizacion']) ? (float)$propuesta['cotizacion'] : 0.00;
                    $cantidad = isset($propuesta['cantidad']) ? $propuesta['cantidad'] : 0;
                    $targetcost = isset($producto->targetcost) ? $producto->targetcost : 0.00;
                    $subtotal = $cotizacion * $cantidad;
                    $subtotaltargetcost = $targetcost * $cantidad;
                    $ahorro_abs = 0.00;
                    $ahorro_porc = 0.00;
                    if ($subtotaltargetcost > 0.00) {
                        $ahorro_abs = $targetcost > 0.00 ? $subtotaltargetcost - $subtotal : 0.00;
                        $ahorro_porc = $targetcost > 0.00 ? (($ahorro_abs / $subtotaltargetcost) * 100) : 0.00;
                    }


                    array_push($values, [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'cotizacion' => $cotizacion,
                        'cantidad' => (int) $cantidad,
                        'fecha' => $propuesta['fecha'] ? $propuesta['fecha'] : 0,
                        'subtotal' => $subtotal,
                        'oferente_id' => $oferente->company->id,
                        'razonSocial' => $oferente->company->business_name,
                        'tipoAdjudicacion' => $oferente->adjudicacion,
                        'moneda' => $concurso->tipo_moneda->nombre,
                        'unidad' => $producto->unidad_medida->name,
                        'targetcost' => $targetcost,
                        'ahorro_porc' => $ahorro_porc,
                        'ahorro_abs' => $ahorro_abs,
                        'tipoValorOferta' => $oferente->tipo_valor_ofertar,
                        'isMenorCantidad' => false,
                        'isMenorPlazo' => false,
                        'isMejorCotizacion' => false
                    ]);
                    $total += $subtotal;
                    $totaltargetcost += $subtotaltargetcost;
                }

                $totalahorro_abs = $totaltargetcost > 0.00 ? $totaltargetcost - $total : 0.00;
                $totalahorro_porc = $totaltargetcost > 0.00 ? ($totalahorro_abs / $totaltargetcost) * 100 : 0.00;
                $posicion = array_search($economicByRound->payment_deadline, array_column(Participante::PLAZOS_PAGO, 'id'));
                $condicionId = array_search($economicByRound->payment_condition, array_column(Participante::CONDICIONES_PAGO, 'id'));
                $ConcursoEconomicas = [
                    'OferenteId' => $oferente->id,
                    'oferente_id' => $oferente->company->id,
                    'nombreOferente' => $oferente->company->business_name,
                    'razonSocial' => $oferente->company->business_name,
                    'tipoAdjudicacion' => $oferente->adjudicacion,
                    'tipoValorOferta' => $oferente->tipo_valor_ofertar,
                    'items' => count($values) > 0 ? $values : [],
                    'total' => $total,
                    'ahorro_abs' => $totalahorro_abs,
                    'ahorro_porc' => $totalahorro_porc,
                    'mejorOfertaIntegral' => false,
                    'difvsmejorofert' => 0.00,
                    'evaluationalcanzada' => $evaluationalcanzada,
                    'file_path' => filePath($oferente->file_path),
                    'porpuesta_economica' => $economic_proposal ? getEconomicDoc($economicByRound) : null,
                    'planilla_costos' => $economic_proposal ? getCostDocument($concurso, $economicByRound) : null,
                    'analisis_apu' => $economic_proposal ? getApuDocument($concurso, $economicByRound) : null,
                    'comentarios' => $economic_proposal ? $economicByRound->comment : null,
                    'fechaPresentacion'    => $economic_proposal
                                ? $economicByRound->updated_at->format('d-m-Y H:i')
                                : null,
                    'cuit' => $oferente->company->cuit,
                    'plazoPago' => Participante::PLAZOS_PAGO[$posicion]['text'],
                    'condicionPago' => Participante::CONDICIONES_PAGO[$condicionId]['text'],
                    'isRechazado' => $oferente->is_concurso_rechazado,
                    'isVencido' => false
                ];
                $oferenteItems = $ConcursoEconomicas;
            } else {
                $ConcursoEconomicas = [
                    'OferenteId' => $oferente->id,
                    'oferente_id' => $oferente->company->id,
                    'nombreOferente' => $oferente->company->business_name,
                    'razonSocial' => $oferente->company->business_name,
                    'tipoAdjudicacion' => null,
                    'tipoValorOferta' => null,
                    'items' => [],
                    'total' => null,
                    'ahorro_abs' => null,
                    'ahorro_porc' => null,
                    'difvsmejorofert' => null,
                    'mejorOfertaIntegral' => false,
                    'evaluationalcanzada' => null,
                    'file_path' => filePath($oferente->file_path),
                    'porpuesta_economica' => null,
                    'planilla_costos' => null,
                    'analisis_apu' => null,
                    'comentarios' => null,
                    'fechaPresentacion' => null,
                    'cuit' => $oferente->company->cuit,
                    'plazoPago' => null,
                    'condicionPago' => null,
                    'isRechazado' => $oferente->is_concurso_rechazado,
                    'isVencido' => true
                ];
                $oferenteItems = $ConcursoEconomicas;
            }
        } else {
            $ConcursoEconomicas = [
                'OferenteId' => $oferente->id,
                'oferente_id' => $oferente->company->id,
                'nombreOferente' => $oferente->company->business_name,
                'razonSocial' => $oferente->company->business_name,
                'tipoAdjudicacion' => null,
                'tipoValorOferta' => null,
                'items' => [],
                'total' => null,
                'ahorro_abs' => null,
                'ahorro_porc' => null,
                'difvsmejorofert' => null,
                'mejorOfertaIntegral' => false,
                'evaluationalcanzada' => null,
                'file_path' => filePath($oferente->file_path),
                'porpuesta_economica' => null,
                'planilla_costos' => null,
                'analisis_apu' => null,
                'comentarios' => null,
                'fechaPresentacion' => null,
                'cuit' => $oferente->company->cuit,
                'plazoPago' => null,
                'condicionPago' => null,
                'isRechazado' => $oferente->is_concurso_rechazado,
                'isVencido' => true
            ];
            $oferenteItems = $ConcursoEconomicas;
        }
    }

    if ($oferente->is_concurso_rechazado) {
        $ConcursoEconomicas = [
            'OferenteId' => $oferente->id,
            'oferente_id' => $oferente->company->id,
            'nombreOferente' => $oferente->company->business_name,
            'razonSocial' => $oferente->company->business_name,
            'tipoAdjudicacion' => null,
            'tipoValorOferta' => null,
            'items' => [],
            'total' => null,
            'ahorro_abs' => null,
            'ahorro_porc' => null,
            'mejorOfertaIntegral' => false,
            'evaluationalcanzada' => null,
            'file_path' => filePath($oferente->file_path),
            'porpuesta_economica' => null,
            'planilla_costos' => null,
            'analisis_apu' => null,
            'comentarios' => null,
            'fechaPresentacion' => null,
            'cuit' => $oferente->company->cuit,
            'plazoPago' => null,
            'isRechazado' => $oferente->is_concurso_rechazado
        ];
        $oferenteItems = $ConcursoEconomicas;
    }


    return $oferenteItems;
}

function setMejorIntegral($concurso, $oferentes)
{
    $mejorIntegral = [];
    $idOferente = 0;
    $mejorOfertaIntegral = 0.00;
    $nombreOferente = '';
    $razonSocial = '';
    $items = [];
    $TipoAdjudicacion = '';
    $productos = $concurso->productos;
    $mejor_ahorro_porc = 0.00;
    $mejor_ahorro_abs = 0.00;
    
    foreach ($oferentes as $i => $oferente) {
        $oferIsEnable = false;
        if ($oferente['isRechazado']) {
            $oferIsEnable = false;
            break;
        }
        foreach ($oferente['items'] as $item) {
            $productOferer = $item['id'];
            $productConcurso = $productos->find($productOferer);
            if ($item['cotizacion'] > 0.00 && $item['cantidad'] == $productConcurso->cantidad) {
                $oferIsEnable = true;
            } else {
                $oferIsEnable = false;
                break;
            }
        }

        if ($oferIsEnable) {
            $total = $oferente['total'];
            $ahorro_porc = $oferente['ahorro_porc'] != 0.00 ? $oferente['ahorro_porc'] : 0.00;
            $ahorro_abs = $oferente['ahorro_abs'] != 0.00 ? $oferente['ahorro_abs'] : 0.00;
            if (isMejorOferta($concurso, $total, $mejorOfertaIntegral) || $mejorOfertaIntegral === 0.00) {
                $idOferente = $oferente['oferente_id'];
                $mejorOfertaIntegral = $total;
                $mejor_ahorro_porc = $ahorro_porc;
                $mejor_ahorro_abs = $ahorro_abs;
                $TipoAdjudicacion = $oferente['tipoAdjudicacion'];
                $items = $oferente['items'];
                $nombreOferente = $oferente['nombreOferente'];
                $razonSocial = $oferente['razonSocial'];
            }
        }

    }

    $mejorIntegral = [
        'idOferente' => $idOferente,
        'nombreOferente' => $nombreOferente,
        'razonSocial' => $razonSocial,
        'tipoAdjudicacion' => $TipoAdjudicacion,
        'total' => $mejorOfertaIntegral,
        'ahorro_porc' => $mejor_ahorro_porc,
        'ahorro_abs' => $mejor_ahorro_abs,
        'items' => $items
    ];

    return $mejorIntegral;
}

function setMejorIndividual($concurso, $oferentes)
{
    $mejorIndividual = [];
    $TipoAdjudicacion = null;
    /**
     * MEJOR OFERTA INDIVIDUAL
     */
    $mejorIndividual = [];
    $i = 0;
    foreach ($concurso->productos as $producto) {
        $mejores_plazos = [];
        $mejores_cantidades = [];
        $mejores_cotizaciones = [];
        if (count($oferentes) > 0) {
            foreach ($oferentes as $row) {
                if (count($row['items']) > 0) {
                    $mejores_plazos[$row['OferenteId']] = $row['items'][$i]['fecha'];
                    $mejores_cantidades[$row['OferenteId']] = $row['items'][$i]['cantidad'];
                    $mejores_cotizaciones[$row['OferenteId']] = $row['items'][$i]['cotizacion'];
                }
            }
            $mejores_cantidades = array_filter(
                $mejores_cantidades,
                function ($value, $key) use ($mejores_cantidades, $producto) {
                    return $value > 0 && $value == $producto->cantidad;
                },
                ARRAY_FILTER_USE_BOTH
            );

            $mejores_plazos = array_intersect_key($mejores_plazos, $mejores_cantidades);
            $mejores_plazos = array_filter(
                $mejores_plazos,
                function ($value, $key) use ($mejores_plazos) {
                    return $value > 0 && $value <= min(array_filter($mejores_plazos));
                },
                ARRAY_FILTER_USE_BOTH
            );

            $mejores_cotizaciones = array_intersect_key($mejores_cotizaciones, $mejores_cantidades);
            $mejores_cotizaciones = array_filter(
                $mejores_cotizaciones,
                function ($value, $key) use ($mejores_cotizaciones, $concurso) {
                    if (
                        $concurso->is_online &&
                        $concurso->tipo_valor_ofertar === 'ascendente'
                    ) {
                        return $value >= max(array_filter($mejores_cotizaciones));
                    }
                    return $value <= min(array_filter($mejores_cotizaciones));
                },
                ARRAY_FILTER_USE_BOTH
            );
            foreach ($oferentes as $index => $row) {
                if (count($row['items']) > 0) {
                    if (!$row['items'][$i]['cotizacion']) {
                        continue;
                    }

                    // Cotización
                    $oferentes[$index]['items'][$i]['isMejorCotizacion'] = isset($mejores_cotizaciones[$row['OferenteId']]);
                    // Cantidad
                    $oferentes[$index]['items'][$i]['isMenorCantidad'] = isset($mejores_cantidades[$row['OferenteId']]);
                    // Plazo
                    $oferentes[$index]['items'][$i]['isMenorPlazo'] = isset($mejores_plazos[$row['OferenteId']]);

                    if ($oferentes[$index]['items'][$i]['isMejorCotizacion']) {
                        $cotizacion = isset($row['items'][$i]['cotizacion']) ? $row['items'][$i]['cotizacion'] : 0.00;
                        $cantidad = isset($row['items'][$i]['cantidad']) ? $row['items'][$i]['cantidad'] : 0;
                        $targetcost = isset($producto->targetcost) ? $producto->targetcost : 0.00;
                        $subtotal = $cotizacion * $cantidad;
                        $subtotaltargetcost = $targetcost * $cantidad;
                        $ahorro_abs = $subtotaltargetcost > 0.00 ? $subtotaltargetcost - $subtotal : 0.00;
                        $ahorro_porc = $subtotaltargetcost > 0.00 ? ($ahorro_abs / $subtotaltargetcost) * 100 : 0.00;

                        $mejorIndividual[$i] = [
                            'idOferente' => $row['oferente_id'],
                            'nombreOferente' => $row['nombreOferente'],
                            'razonSocial' => $row['razonSocial'],
                            'itemId' => $row['items'][$i]['id'],
                            'itemNombre' => $row['items'][$i]['nombre'],
                            'itemCotizacion' => $cotizacion,
                            'itemCantidad' => $cantidad,
                            'subTotal' => $subtotal,
                            'subtotaltargetcost' => $subtotaltargetcost,
                            'targetcost' => $producto->targetcost,
                            'ahorro_porc' => $ahorro_porc,
                            'ahorro_abs' => $ahorro_abs,
                            'itemFecha' => $row['items'][$i]['fecha'],
                            'tipoAdj' => $row['tipoAdjudicacion'],
                            'tipoValorOferta' => $row['tipoValorOferta'],
                        ];
                    }
                }
            }

            $i++;
        }
    }

    $total1 = 0.00;
    $total1targetcost = 0.00;
    $idOferentes = [];
    foreach ($mejorIndividual as $k => $v) {
        $TipoAdjudicacion = $v['tipoAdj'];
        $total1 += $v['subTotal'];
        $total1targetcost += $v['subtotaltargetcost'];
        $idOferentes[] = $v['idOferente'] . ':' . $v['itemId'];
    }

    $mejor_ahorro_abs = $total1targetcost > 0.00 ? $total1targetcost - $total1 : 0.00;
    $mejor_ahorro_porc = $total1targetcost > 0.00 ? ($mejor_ahorro_abs / $total1targetcost) * 100 : 0.00;

    $mejorIndividual = [
        'mejorIndividual' => [
            'individual' => array_values($mejorIndividual),
            'total1' => $total1,
            'ahorro_porc' => $mejor_ahorro_porc,
            'ahorro_abs' => $mejor_ahorro_abs,
            'tipoAdj' => $TipoAdjudicacion,
            'idOferentes' => implode(',', $idOferentes),
        ],
        'oferentes' => $oferentes

    ];
    return $mejorIndividual;
}

function setMejorManual($concurso, $ConcursoEconomicas)
{
    /**
     * MEJOR OFERTA MANUAL
     */
    $ids = $concurso->productos->pluck('id');
    $nombres = $concurso->productos->pluck('nombre');
    $cantidades = $concurso->productos->pluck('cantidad');
    $mejorManual = [];
    $ConcursoEconomicasItems = isset($ConcursoEconomicas['items']) ? $ConcursoEconomicas['items'] : [];
    for ($i = 0; $i < count($ids); $i++) {
        $ConcursoEconomicasItem = isset($ConcursoEconomicasItems[$i]) ? $ConcursoEconomicasItems[$i] : [];
        $mejorManual[] =
            $ids[$i] . ',' .
            $cantidades[$i] . ',' .
            $nombres[$i] . ',' .
            (isset($ConcursoEconomicasItem['moneda']) ? $ConcursoEconomicasItem['moneda'] : '') . ',' .
            (isset($ConcursoEconomicasItem['unidad']) ? $ConcursoEconomicasItem['unidad'] : '') . ',' .
            (isset($ConcursoEconomicasItem['fecha']) ? $ConcursoEconomicasItem['fecha'] : '');
    }

    return $mejorManual;
}