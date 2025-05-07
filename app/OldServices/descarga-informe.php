<?php

/**
 * Arma un archivo PDF a partir de un array multidimensional y lo envía por el
 * navegador.
 *
 * El array puede tener una o más de estas estructuras:
 *
 * {
 *     'titulo': (string) Titulo de la sección (texto azul en negrita)
 *     'contenido': [{SECCION}, {SECCION}, {SECCION}, ...],
 * }
 *
 * Cada SECCION debe ser un array asociativo como el siguiente:
 * 
 * {
 *     'tipo': (string) Tipo de contenido, puede ser 'tabla' o 'parrafo'.
 *     
 *     'cabeceras' (array) Array de arrays que representan filas <tr> dentro de
 *                         <thead>. Sólo se usa cuando 'tipo' es 'tabla'.
 *
 *     'contenido': (string/array) Si 'tipo' es 'parrafo' se debe usar un string.
 *                                 Si 'tipo' es tabla se debe usar un array de
 *                                 arrays, que representa filas <tr> dentro de
 *                                 <tbody>.
 * },
 *
 * Por ejemplo:
 * {
 *     'tipo': 'parrafo',
 *     'contenido': '<p>Contenido del parrafo con
 *                     <strong style="color:red">reducido soporte HTML y
 *                     CSS</strong>. Debe estar bien formateado</p>'
 * },
 * {
 *     'tipo': 'tabla',
 *     'cabeceras': [
 *         [ CELDA, CELDA, CELDA, ],
 *         ...
 *     ],
 *     'contenido': [
 *         [ CELDA, CELDA, CELDA, ],
 *         [ CELDA, CELDA, CELDA, ],
 *         [ CELDA, CELDA, CELDA, ],
 *         ...
 *     ]
 * }
 * 
 * Cada CELDA puede ser un string representando el valor de la misma (el contenido
 * de <td>), ejemplo:
 * 
 * 'contenido':
 *     [
 *         [
 *             '10/10',
 *             'Oferente 1',
 *         ],
 *         [
 *             '11/10',
 *             'Oferente 2',
 *         ],
 *     ],
 *     ...
 *
 * O si se requiere personalizar la CELDA se puede usar un array, ejemplo:
 *
 * 'contenido':
 *     [
 *         [
 *             '10/10',
 *             'Oferente 1',
 *         ],
 *         [
 *             '11/10',
 *             {
 *                 'valor': 'Oferente 2',
 *                 'css': {
 *                     'background-color: green',
 *                     'color': 'red',
 *                     'font-weight': 'bold',
 *                 },
 *                 'attr': {
 *                     'align': 'center'
 *                 },
 *             },
 *         ],
 *     ],
 *
 *
 * ===============================
 * EL SOPORTE HTML/CSS ES REDUCIDO
 * ===============================
 *     - no acepta:
 *         - margin
 *         - padding
 *         - float
 *         - background (pero sí background-color)
 *
 *     - Tags soportados: a, b, blockquote, br, dd, del, div, dl, dt, em, font,
 *                        h1, h2, h3, h4, h5, h6, hr, i, img, li, ol, p, pre,
 *                        small, span, strong, sub, sup, table, tcpdf, td, th,
 *                        thead, tr, tt, u, ul
 *
 */

require 'rest.php';
require 'tcpdf/tcpdf.php';
require 'calculos-etapas.php';

header("Access-Control-Allow-Origin: *");
// header("Content-type: application/pdf");

function formatearFecha($string) 
{
    $array = explode('-', $string);
    if (count($array) === 3) {
        $string = $array[2] . '/' . $array[1] . '/' . $array[0];
    }
    return $string;
}

class DataInforme extends Rest 
{
    private $concurso = null;
    private $rondaActualConcurso = null;
    private $rondaConcursoArrayResult = null;


    public function __construct($idConcurso) 
    {

        $this->concurso = \App\Models\Concurso::find(abs(intval($idConcurso)));
        $this->rondaActualConcurso = $this->concurso->ronda_actual;
        $this->rondaConcursoArrayResult = $this->rondaActualConcurso - 1;

        

        parent::__construct();

        // Debug
        $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Requerido para usar calcularEtapaAnalisisOfertas()
     */
    public function reverse($date) 
    {
        $d = explode('-', $date);
        return $d[2] . "-" . $d[1] . "-" . $d[0];
    }

    private function seccionPreparacion() 
    {
        $fechaConvocatoria = $this->concurso->oferentes->first()->invitation->created_at->format('d/m/Y');

        $archivos = [];
        foreach ($this->concurso->sheets as $sheet) {
            // La librería de PDF no soporta target="_blank", pero lo dejo
            // en el código por si algún día sí.
            $archivos[] = [
                $sheet->type->description,
                sprintf(
                        '<a target="_blank" href="%1$s">Enlace</a>', 
                        $sheet->filename
                )
            ];
        }

        return [
            'titulo' => 'Preparación',
            'contenido' => [
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<p>Fecha de envío de invitaciones: ' . $fechaConvocatoria . '</p>',
                ],
                [
                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Documento / Archivo',
                            'URL',
                        ]
                    ],
                    'contenido' => $archivos,
                ]
            ]
        ];
    }

    private function seccionParametros() 
    {
        $contenido = [];
        $nuevasRondas = \App\Models\Concurso::NUEVAS_RONDAS;
        $fechasNuevasRondas = \App\Models\Concurso::CAMPOS_FECHA_NUEVA_RONDA;
        
        $tipo_convocatoria = [
            'Tipo de convocatoria',
            $this->concurso->alcance->nombre,
        ];

        $muro_consultas = [
            'Fecha cierre muro consultas',
            $this->concurso->finalizacion_consultas->format('d/m/Y H:i:s'),
        ];

        $pliego = [
            'Fecha aceptación pliegos, términos y condiciones',
            $this->concurso->fecha_limite->format('d/m/Y H:i:s'),
        ];

        $tecnica = [
            'Fecha límite oferta técnica',
            $this->concurso->technical_includes ? 
            $this->concurso->ficha_tecnica_fecha_limite->format('d/m/Y H:i:s') : 
            'No aplica.',
        ];

        $economica = [
            'Fecha límite oferta económica',
            $this->concurso->is_online ? 
            $this->concurso->inicio_subasta->format('d/m/Y H:i:s') :
            $this->concurso->fecha_limite_economicas->format('d/m/Y H:i:s'),
        ];

        $rondas = [
            'Cantidad de Rondas ejecutadas',
            (string) $this->concurso->ronda_actual
        ];
        
        $contenido [] = $tipo_convocatoria;
        $contenido [] = $muro_consultas;
        $contenido [] = $pliego;
        $contenido [] = $tecnica;
        $contenido [] = $economica;
        $contenido [] = $rondas;
        

        if($this->rondaActualConcurso > 1){
            for ($i = 2; $i<=$this->rondaActualConcurso; $i++){
                $contenido[] = [
                    'Fecha límite '.$nuevasRondas[$i], $this->concurso[$fechasNuevasRondas[$i]]->format('d/m/Y H:i:s')
                ];
            }
        }

        $adjAnticipada = [
            'Adjudicación anticipada',
            ucfirst($this->concurso->finalizar_si_oferentes_completaron_economicas),
        ];

        $cancelacion = [
            'Fecha Cancelación',
            $this->concurso->trashed() ? 
            $this->concurso->deleted_at->format('d/m/Y H:i:s') : 
            ''
        ];

        $userCancelacion = [
            'Usuario Cancelación',
            $this->concurso->trashed() ? 
            (
                $this->concurso->usuario_cancelacion ? 
                $this->concurso->usuario_cancelacion->full_name : 
                'Proceso automático'
            ) : ''
        ];

        $contenido [] = $adjAnticipada;
        $contenido [] = $cancelacion;
        $contenido [] = $userCancelacion;

        
        return [

            'titulo' => '1. Parámetros del concurso',
            'contenido' => [
                [

                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Parámetro',
                            'Valor / Configuración',
                        ]
                    ],
                    'contenido' => $contenido
                ]
            ]
        ];
    }

    private function seccionConvocatoriaOferentes() 
    {
        $filas = [];
        foreach ($this->concurso->oferentes as $oferente) {
            $filas[] = [
                $oferente->company->business_name,
                $oferente->invitation->created_at->format('d/m/Y'),
                $oferente->invitation->status->description,
                $oferente->invitation->updated_at ? $oferente->invitation->updated_at->format('d/m/Y') : '',
            ];
        }

        return [
            'titulo' => '2. Convocatoria de Proveedores',
            'contenido' => [
                [
                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Proveedor',
                            'Fecha invitación',
                            'Términos y condiciones',
                            'Fecha aceptación términos y condiciones',
                        ]
                    ],
                    'contenido' => $filas,
                ]
            ]
        ];
    }

    private function seccionPresentacionOfertas() 
    {
        if ($this->concurso->technical_includes) {
            $oferentes = $this->concurso->oferentes->where('has_tecnica_aprobada');
        } else {
            $oferentes = $this->concurso->oferentes->where('has_invitacion_aceptada');
        }
        
        $rondasOfertasTitle = [
            1 => '1ª Ronda de Ofertas',
            2 => '2ª Ronda de Ofertas',
            3 => '3ª Ronda de Ofertas',
            4 => '4ª Ronda de Ofertas',
            5 => '5ª Ronda de Ofertas'
        ];
        $contenido = [];
        
        for ($i=1; $i <= $this->rondaActualConcurso; $i++) {
            $contenidoTabla = [];
            
            foreach($oferentes as $oferente){
                if($oferente->is_economica_pendiente){
                    $contenidoTabla[]=[
                        $oferente->company->business_name,
                        'El proveedor no presentó su propuesta en la fecha establecida',
                        $oferente->reasonDeclination
                    ];
                }else{
                    $economicProposal = $oferente->economic_proposal->where('participante_id', $oferente->id)->where('numero_ronda', $i)->first();
                    $total = $this->calcEconomics($economicProposal->values);
                    if($oferente->is_concurso_rechazado && !$economicProposal){
                        $contenidoTabla[]=[
                            $oferente->company->business_name,
                            'El proveedor declinó su participacion',
                            $oferente->reasonDeclination
                        ];
                    }else{
                        $contenidoTabla[]=[
                            $oferente->company->business_name,
                            $oferente->has_economica_presentada ? $economicProposal->created_at->format('d/m/Y H:i:s') : '',
                            (string)(number_format($total,2,',', '.'))
                        ];
                    }
                }
                
            }
            $parrafo = [
                'tipo' => 'parrafo',
                'contenido' => '<div style="font-weight:bold">'.$rondasOfertasTitle[$i].'</div>',
            ];
            $tabla = [
                'tipo' => 'tabla',
                'cabeceras' => [
                    [
                        'Proveedor',
                        'Fecha',
                        'Cotización'
                    ]
                ],
                'contenido' => $contenidoTabla,
            ];
            $contenido[] = $parrafo;
            $contenido[] = $tabla;
        }        
        return [
            'titulo' => '3. Presentacion de ofertas',
            'contenido' => $contenido
        ];
    }

    private function seccionAdjudicacion() 
    {
        $list = [
            'RondasOfertas' => [],
        ];

        $contenidoSinOfertas = [
            'titulo' => '4. Adjudicación',
            'contenido' => [
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<div style="text-align:center;font-weight:bold">Mejor oferta </div>',
                ],
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<div style="text-align:center;font-weight:bold">Sin Ofertas de proveedores</div>',
                ]
            ]
        ];

        $contenidoSinAdjudicar = [
            'titulo' => '4. Adjudicación',
            'contenido' => [
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<div style="text-align:center;font-weight:bold">Mejor oferta </div>',
                ],
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<div style="text-align:center;font-weight:bold">Aun no se adjudica la'.$this->concurso->tipo_concurso_nombre.'</div>',
                ]
            ]
        ];
        
        

        $tipoAdjudicacion = $this->concurso::TIPO_ADJUDICACION;

        calcularEtapaAnalisisOfertas($list, $this->concurso->id);
        $ofertaActual = $list['RondasOfertas'][$this->rondaConcursoArrayResult];
        
        if (empty($ofertaActual['ConcursoEconomicas']['proveedores'])) {
            return $contenidoSinOfertas;
        }

        if(!$this->concurso->adjudicado){
            return $contenidoSinAdjudicar;
        }

        if($this->concurso->adjudicado){
            $id_adjudicacion = $this->concurso->oferentes
            ->whereIn('adjudicacion', $this->concurso::TIPO_ADJUDICACION)
            ->first()->adjudicacion;

            $tipoAdjudicacion = array_search($id_adjudicacion, $this->concurso::TIPO_ADJUDICACION);
            $comentario = $this->concurso->adjudicacion_comentario;

            if($tipoAdjudicacion == 'integral'){
                $mejorIntegral = $ofertaActual['ConcursoEconomicas']['mejoresOfertas']['mejorIntegral'];
                $proveedores = $ofertaActual['ConcursoEconomicas']['proveedores'];
                return $this->setIntegral($mejorIntegral, $proveedores, $comentario);
            }

            if($tipoAdjudicacion == 'individual'){
                $mejorIndividual = $ofertaActual['ConcursoEconomicas']['mejoresOfertas']['mejorIndividual'];
                $proveedores = $ofertaActual['ConcursoEconomicas']['proveedores'];
                return $this->setIndividual($mejorIndividual, $proveedores, $comentario);
            }

            if($tipoAdjudicacion == 'manual'){
                $adjudicacionManual = $this->concurso->adjudicacion_items;
                return $this->setManual($adjudicacionManual, $comentario);
            }
        }
    }

    private function seccionAceptacionAdjudicaciones() 
    {
        return [
            'titulo' => 'Aceptacion de adjudicaciones',
            'contenido' => [
                [
                    'tipo' => 'parrafo',
                    'contenido' => '[POR HACER] Fecha y hora de aceptación de ofertas de cada proveedor',
                ]
            ]
        ];
    }

    private function seccionReputacion() 
    {
        // Una tabla por cada fila.
        $tablas = [];
        foreach ($this->concurso->oferentes as $oferente) {
            $evaluacion = $oferente->evaluacion;
            if (!$evaluacion) {
                continue;
            }
            $valores = json_decode($evaluacion->valores, true);
            // Item             = index en db = index en array
            // No cumple        = 1           = 1
            // Cumple levemente = 2           = 2
            // Cumple           = 3           = 3
            // Supera           = 4           = 4
            // No aplica        = 0           = 5
            foreach ($valores as $k => $v) {
                if ($k) {
                    if ($v === '0') {
                        $v = '5';
                    }
                    $valores[$k] = intval($v);
                } else {
                    unset($valores[$k]);
                }
            }

            $marca = [
                'valor' => 'X',
                'attr' => ['align' => 'center'],
            ];

            $filasTabla = [];

            $filasTabla[0] = array_fill(0, 6, '');
            $filasTabla[0][0] = 'Puntualidad';
            if (isset($valores['Puntualidad'])) {
                $filasTabla[0][($valores['Puntualidad'])] = $marca;
            }

            $filasTabla[1] = array_fill(0, 6, '');
            $filasTabla[1][0] = 'Calidad';
            if (isset($valores['Calidad'])) {
                $filasTabla[1][($valores['Calidad'])] = $marca;
            }

            $filasTabla[2] = array_fill(0, 6, '');
            $filasTabla[2][0] = 'Orden y limpieza';
            if (isset($valores['OrdenYlimpieza'])) {
                $filasTabla[2][($valores['OrdenYlimpieza'])] = $marca;
            }

            $filasTabla[3] = array_fill(0, 6, '');
            $filasTabla[3][0] = 'Medio ambiente';
            if (isset($valores['MedioAmbiente'])) {
                $filasTabla[3][($valores['MedioAmbiente'])] = $marca;
            }

            $filasTabla[4] = array_fill(0, 6, '');
            $filasTabla[4][0] = 'Higiene y seguridad';
            if (isset($valores['HigieneYseguridad'])) {
                $filasTabla[4][($valores['HigieneYseguridad'])] = $marca;
            }

            $filasTabla[5] = array_fill(0, 6, '');
            $filasTabla[5][0] = 'Experiencia';
            if (isset($valores['Experiencia'])) {
                $filasTabla[5][($valores['Experiencia'])] = $marca;
            }

            $filasTabla[6] = [
                'Comentarios',
                [
                    'valor' => $evaluacion->comentario,
                    'attr' => [
                        'colspan' => 5,
                    ]
                ]
            ];

            $tablas[] = [
                'tipo' => 'tabla',
                'cabeceras' => [
                    [
                        [
                            'valor' => $oferente->user->offerer_company->business_name,
                            'attr' => ['colspan' => 6],
                        ]
                    ],
                    [
                        'Item',
                        'No cumple expectativas',
                        'Cumple levemente las expectativas',
                        'Cumple las expectativas',
                        'Supera las expectativas',
                        'No aplica',
                    ]
                ],
                'contenido' => $filasTabla,
            ];
        }

        if (!$tablas) {
            $tablas = [
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<em style="color:#888">Sin información disponible.</em>',
                ]
            ];
        }

        return [
            'titulo' => 'Reputación',
            'contenido' => $tablas
        ];
    }

    public function obtenerInformacion() 
    {
        $title ='';
        
        $head = '';
        if($this->concurso->is_online){
            $title = 'Informe concurso subasta';
            $head = 'REPORTE CONCURSO SUBASTA';
        }
        if($this->concurso->is_sobrecerrado){
            $title = 'Informe concurso licitación';
            $head = 'REPORTE CONCURSO LICITACIÓN';
        }
        if($this->concurso->is_go){
            $title = 'Informe concurso go';
            $head = 'REPORTE CONCURSO GO';
        }
        
        $retorno = [];
        
        $retorno['nombreConcurso'] = $this->concurso->nombre;
        $retorno['logo'] = publicPath(asset('/global/img/logo.png'));
        $retorno['title'] = $title;
        $retorno['header'] = $head;
        $retorno[] = $this->seccionPreparacion();
        $retorno[] = $this->seccionParametros();
        $retorno[] = $this->seccionConvocatoriaOferentes();
        $retorno[] = $this->seccionPresentacionOfertas();        
        $retorno[] = $this->seccionAdjudicacion();
        $retorno[] = $this->seccionReputacion();
        return $retorno;
        
    }

    /**
     * Arma la tabla de comparativas.
     *
     * @param array $list Retorno de calcularEtapaAnalisisOfertas()
     * @return array
     */
    private function armarTablaComparativaOfertas($proveedores) 
    { 
        $retorno = [
            'tipo' => 'tabla',
            'opciones' => ['resumen-ofertas'],
            'cabeceras' => [
                [
                    [
                        'valor' => 'Resumen de ofertas',
                        'css' => [
                            'font-size' => '14'
                        ],
                        'attr' => [
                            'colspan' => '3'
                        ]
                    ]
                ],
                [
                    'PROVEEDOR',
                    'ITEM',
                    'Total',
                ],
                [
                    '',
                    'Moneda',
                    '',
                ],
                [
                    '',
                    'Unidad de medida',
                    '',
                ],
                [
                    '',
                    'Cantidad solicitada',
                    '',
                ]
            ],
            'contenido' => [],
        ];

        
        // Cabeceras
        foreach ($proveedores[0]['items'] as $item) {
            $retorno['cabeceras'][0][0]['attr']['colspan'] ++;
            $retorno['cabeceras'][1][] = $item['nombre'];
            $retorno['cabeceras'][2][] = $item['moneda'];
            $retorno['cabeceras'][3][] = $item['unidad'];
            $retorno['cabeceras'][4][] = [
                'valor' => $item['cantidad'],
                'css' => ['color' => '#FF0000', 'font-weight' => 'bold'],
            ];
        }
        
        
        // Filas "Cotización"
        foreach ($proveedores as $oferente) {
            if(!$oferente['isRechazado']){
                $fila = [
                    $oferente['nombreOferente'],
                    [
                        'valor' => 'Cotización',
                        'css' => ['font-size' => '10pt'],
                    ],
                    strval($oferente['total'])
                ];
                foreach ($oferente['items'] as $valor) {
                    if ($valor['isMenorCotizacion']) {
                        $fila[] = [
                            'valor' => $valor['cotizacion'],
                            'css' => [
                                'background-color' => '#c6e0b4',
                                'font-weight' => 'bold',
                            ]
                        ];
                    } else {
                        $fila[] = $valor['cotizacion'];
                    }
                }
                $retorno['contenido'][] = $fila;
            }
            
        }

        // Filas "Cantidad cotizada"
        foreach ($proveedores as $oferente) {
            if(!$oferente['isRechazado']){
                $fila = [
                    $oferente['nombreOferente'],
                    [
                        'valor' => 'Cantidad cotizada',
                        'css' => [
                            'font-size' => '10pt',
                        ]
                    ],
                    '',
                ];
                foreach ($oferente['items'] as $valor) {
                    if ($valor['isMenorCantidad']) {
                        $fila[] = [
                            'valor' => $valor['cantidad'],
                            'css' => [
                                'background-color' => '#c6e0b4',
                                'font-weight' => 'bold',
                            ]
                        ];
                    } else {
                        $fila[] = $valor['cantidad'];
                    }
                }
                $retorno['contenido'][] = $fila;
            }
        }

        // Filas "Plazo de entrega"
        foreach ($proveedores as $oferente) {
            if(!$oferente['isRechazado']){
                $fila = [
                    $oferente['nombreOferente'],
                    [
                        'valor' => 'Plazo de entrega (días)',
                        'css' => [
                            'font-size' => '10pt',
                        ]
                    ],
                    '',
                ];
                foreach ($oferente['items'] as $valor) {
                    if ($valor['isMenorPlazo']) {
                        $fila[] = [
                            'valor' => strval($valor['fecha']),
                            'css' => [
                                'background-color' => '#c6e0b4',
                                'font-weight' => 'bold',
                            ]
                        ];
                    } else {
                        $fila[] = strval($valor['fecha']);
                    }
                }
                $retorno['contenido'][] = $fila;
            }
        }

        return $retorno;
    }

    private function calcEconomics($values){
        $total = 0;
        foreach ($values as $value) {
            $total = $total + $value['total'];
        }
        return $total;
    }

    private function setIntegral($mejorIntegral, $proveedores, $comentario)
    {
        $contenidoMejorOfertaIntegral = [];
        foreach ($mejorIntegral['items'] as $item) {
        
            $contenidoMejorOfertaIntegral[] = [
                $item['nombre'],
                strval(number_format($item['subtotal'], 2, ',', '.')),
                $item['razonSocial'],
            ];
        }

        $contenidoMejorOfertaIntegral[] = [
            [
                'valor' => 'Oferta total Integral',
                'css' => ['font-weight' => 'bold']
            ],
            [
                'valor' => $mejorIntegral['total'],
                'css' => ['font-weight' => 'bold']
            ],
            $mejorIntegral['razonSocial'],
        ];

        $tablaComparativaOfertas = $this->armarTablaComparativaOfertas($proveedores);

        return [
            'titulo' => '4. Adjudicación',
            'contenido' => [
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<div style="text-align:center;font-weight:bold">Mejor oferta Integral</div>',
                ],
                [
                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Item',
                            'Cotización',
                            'Proveedor'
                        ]
                    ],
                    'contenido' => $contenidoMejorOfertaIntegral,
                ],
                [
                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Comentarios'
                        ]
                    ],
                    'contenido' => [[
                        'valor' => $comentario ? $comentario:'<em>No hay comentarios</em>',
                        'css' => ['font-weight' => 'bold']
                    ]]
                ],
                $tablaComparativaOfertas,
            ],
        ];
    }
    private function setIndividual($mejorIndividual, $proveedores, $comentario)
    {
        $contenidoMejorOfertaIndividual = [];
                    
        foreach ($mejorIndividual['individual'] as $item) {
            
            $contenidoMejorOfertaIndividual[] = [
                $item['itemNombre'],
                strval(number_format($item['subTotal'], 2, ',', '.')),
                $item['razonSocial'],
            ];
        }
        $contenidoMejorOfertaIndividual[] = [
            [
                'valor' => 'Oferta total',
                'css' => ['font-weight' => 'bold']
            ],
            [
                'valor' => $mejorIndividual['total1'],
                'css' => ['font-weight' => 'bold']
            ],
            $mejorIndividual['razonSocial'],
        ];
        $tablaComparativaOfertas = $this->armarTablaComparativaOfertas($proveedores);
        
        return [
            'titulo' => '4. Adjudicación',
            'contenido' => [
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<div style="text-align:center;font-weight:bold">Mejor oferta Individual </div>',
                ],
                [
                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Item',
                            'Cotización',
                            'Proveedor'
                        ]
                    ],
                    'contenido' => $contenidoMejorOfertaIndividual,
                ],
                [
                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Comentarios'
                        ]
                    ],
                    'contenido' => [[
                        'valor' => $comentario ? $comentario:'<em>No hay comentarios</em>',
                        'css' => ['font-weight' => 'bold']
                    ]]
                ],
                $tablaComparativaOfertas,
            ]
        ];
    }
    private function setManual($adjudicacionItems, $comentario)
    {
        $contenidoMejorOfertaManual = [];
        $totalAdjudicacion = 0;
        foreach ($adjudicacionItems as $item) {
            $totalAdj = 0.00;
            $totalAdj = (float)$item['cantidadAdj'] * (float)$item['cotUnitaria'];
            $totalAdjudicacion +=$totalAdj;
            $contenidoMejorOfertaManual[] = [                            
                $item['itemNombre'],
                strval(number_format($item['itemSolicitado'], 2, ',', '.')),
                strval(number_format($item['cantidad'], 2, ',', '.')),
                strval(number_format($item['cotizacion'], 2, ',', '.')),
                strval(number_format($item['cantidadAdj'], 2, ',', '.')),
                strval(number_format($totalAdj, 2, ',', '.')),
                $item['razonSocial'],
            ];
        }
        $contenidoMejorOfertaManual[] = [
            [
                'valor' => '',
                'css' => ['font-weight' => 'bold'],
            ],
            [
                'valor' => '',
                'css' => ['font-weight' => 'bold'],
            ],
            [
                'valor' => '',
                'css' => ['font-weight' => 'bold'],
            ],
            [
                'valor' => '',
                'css' => ['font-weight' => 'bold'],
            ],
            [
                'valor' => 'Oferta total',
                'css' => ['font-weight' => 'bold'],
            ],
            [
                'valor' => strval(number_format($totalAdjudicacion, 2, ',', '.')),
                'css' => ['font-weight' => 'bold'],
            ],
            [
                'valor' => '',
                'css' => ['font-weight' => 'bold'],
            ],
            $adjudicacionItems['razonSocial'],
        ]; 
        return [
            'titulo' => '4. Adjudicación',
            'contenido' => [
                [
                    'tipo' => 'parrafo',
                    'contenido' => '<div style="text-align:center;font-weight:bold">Mejor oferta Manual </div>',
                ],
                [
                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Item',
                            'Cantidad Solicitada',
                            'Cantidad Cotizada',
                            'Cotización',
                            'Cantidad Adjudicada',
                            'Total Adjudicación',
                            'Proveedor',
                        ]
                    ],
                    'contenido' => $contenidoMejorOfertaManual,
                ],
                [
                    'tipo' => 'tabla',
                    'cabeceras' => [
                        [
                            'Comentarios'
                        ]
                    ],
                    'contenido' => [[
                        'valor' => $comentario ? $comentario:'<em>No hay comentarios</em>',
                        'css' => ['font-weight' => 'bold']
                    ]]
                ],
            ],
        ];   
    }

}

class InformePDF extends TCPDF 
{
    protected $logoConcurso = '';
    protected $title = '';
    protected $head = '';

    public function __construct($config) 
    {
        if (!empty($config['logo']) && file_exists($config['logo'])) {
            $this->logoConcurso = $config['logo'];
        }

        parent::__construct('Portrait', 'pt', 'A4');

        $this->SetCreator('Optus');
        $this->SetAuthor('Optus');
        $this->SetTitle($config['title']);

        $this->SetMargins(30, 105, 30);
        $this->SetHeaderMargin(30);

        $this->setPrintFooter(false);

        // set auto page breaks
        $this->SetAutoPageBreak(TRUE, 30);

        // set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $this->AddPage();

        $this->SetFont('helvetica', '', 10, __DIR__ . '/tcpdf/fonts/helvetica.php');
        $this->setColor('text', 119);
        $this->Ln(5);
        $this->Cell(0, 10 * 1.4, $config['header'], 0, 1, 'C', false, '', 0, true, 'T', 'M');

        $this->setColor('text', 32);
        $this->SetFont('helvetica', 'b', 20, __DIR__ . '/tcpdf/fonts/helvetica.php');
        $this->MultiCell(0, 0, $config['nombre-concurso'], 0, 'C');

        $this->Ln(3);
    }

    public function Header() 
    {
        if ($this->header_xobjid === false || $this->CurOrientation !== 'P') {
            $this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);

            // Logo del concurso, de alto fijo.
            if ($this->logoConcurso) {
                $x = $this->original_lMargin;
                $y = $this->header_margin;

                $maxH = 60;
                $dimensiones = getimagesize($this->logoConcurso);
                $w = ceil(($dimensiones[0] * $maxH) / $dimensiones[1]);
                $h = $maxH;

                $this->Image($this->logoConcurso, $x, $y, $w, $h);
            }

            // Logo Optus
            $archivo = asset('/global/img/logo-pdf-optus-gris.gif');
            $x = $this->w - $this->original_rMargin - 120;
            $y = $this->header_margin;
            $this->Image($archivo, $x, $y, 120, 0, 'gif', '', 'N', false, 300, '', false, false, 0, false, false, false);

            // Linea separadora
            $x = $this->original_lMargin;
            $y = (2.835 / $this->k) + max($this->getImageRBY(), $this->y);
            $w = $this->w - $this->original_lMargin - $this->original_rMargin;

            $this->Rect($x, $y, $w, 0.5, 'F', [], array(200));

            $this->endTemplate();
        }

        parent::Header();

        if ($this->CurOrientation !== 'P') {
            $this->resetHeaderTemplate();
        }
    }

    public function crearTitulo($string) 
    {
        $this->SetFont('helvetica', 'b', 16, __DIR__ . '/tcpdf/fonts/helvetica.php');
        $this->setColor('text', 46, 116, 181);
        $this->MultiCell(0, 0, $string, 0, 'L');
        $this->Ln(3);
    }

    public function crearSubTitulo($string) 
    {
        $this->SetFont('helvetica', 'b', 14, __DIR__ . '/tcpdf/fonts/helvetica.php');
        $this->setColor('text', 46, 116, 181);
        $this->MultiCell(0, 0, $string, 0, 'L');
        $this->Ln(3);
    }

    public function agregarContenido($contenido) 
    {
        $this->SetFont('helvetica', '', 12, __DIR__ . '/tcpdf/fonts/helvetica.php');
        if ($contenido['tipo'] === 'tabla') {
            $this->crearTabla($contenido);
        } else if ($contenido['tipo'] === 'parrafo') {
            $this->crearParrafo($contenido);
        }
    }

    public function crearCeldaHTML($celda, $tipo = 'normal') 
    {
        $porDefecto = [
            'valor' => '',
            'css' => [
                'border' => '0.5pt solid #aaa',
                'color' => '#202020',
                'font-size' => '11pt',
            ],
            'attr' => [
                'cellpadding' => '1',
            ]
        ];

        if (is_string($celda)) {
            $celda = array_merge($porDefecto, ['valor' => $celda]);
        } else if (is_array($celda)) {
            if (!isset($celda['css'])) {
                $celda['css'] = [];
            }
            if (!isset($celda['attr'])) {
                $celda['attr'] = [];
            }
            $celda['css'] = array_merge($porDefecto['css'], $celda['css']);
            $celda['attr'] = array_merge($porDefecto['attr'], $celda['attr']);
        } else {
            $celda = $porDefecto;
        }

        if ($tipo === 'cabecera') {
            $celda['attr']['align'] = 'center';
        }

        $estilos = [];
        foreach ($celda['css'] as $prop => $valor) {
            $estilos[] = $prop . ':' . $valor;
        }

        $atributos = [];
        foreach ($celda['attr'] as $prop => $valor) {
            $atributos[] = $prop . '="' . $valor . '"';
        }

        return sprintf(
                '<td %1$s style="%2$s">%3$s</td>', implode(' ', $atributos), implode(';', $estilos), $celda['valor']
        );
    }

    public function crearTabla($contenido) 
    {
        $html = '<table cellpadding="2">';

        if ($contenido['cabeceras']) {
            $html .= '<thead>';
            foreach ($contenido['cabeceras'] as $fila) {
                if (is_array($fila)) {
                    $html .= '<tr style="background-color:#e8e8e8;font-weight:bold">';
                    foreach ($fila as $celda) {
                        $html .= $this->crearCeldaHTML($celda, 'cabecera');
                    }
                    $html .= '</tr>';
                }
            }
            $html .= '</thead>';
        }

        if ($contenido['contenido']) {
            $html .= '<tbody>';
            foreach ($contenido['contenido'] as $fila) {
                $html .= '<tr>';
                foreach ($fila as $celda) {
                    $html .= $this->crearCeldaHTML($celda);
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
        }

        $html .= '</table>';

        if (isset($contenido['opciones'])) {
            if (in_array('resumen-ofertas', $contenido['opciones'])) {
                $this->AddPage('L');
                $this->writeHTML($html);
                $this->AddPage('P');
            }
        } else {
            $this->writeHTML($html);
        }
    }

    public function crearParrafo($contenido) 
    {
        $this->SetFont('helvetica', '', 12, __DIR__ . '/tcpdf/fonts/helvetica.php');
        $this->setColor('text', 40);
        $this->writeHTML($contenido['contenido']);
        $this->Ln(5);
    }

   

}

$dataInforme = new DataInforme($_GET['Id']);
// dd($dataInforme->obtenerInformacion());
$data = $dataInforme->obtenerInformacion();



$informe = new InformePDF([
    'nombre-concurso' => $data['nombreConcurso'],
    'logo' => $data['logo'],
    'title' => $data['title'],
    'header' => $data['header'],
]);

foreach ($data as $index => $seccion) {
    if (!is_int($index)) {
        continue;
    }
    $informe->crearTitulo($seccion['titulo']);
    if(isset(($seccion['subTitulo']))){
        $informe->crearSubTitulo($seccion['subTitulo']);
    }
    if (is_array($seccion['contenido'])) {
        foreach ($seccion['contenido'] as $contenido) {
            $informe->agregarContenido($contenido);
        }
    }
}

//Para suprimir el mensaje:
//TCPDF ERROR: Some data has already been output, can't send PDF file
ob_end_clean();

//$informe->Output('Infome_' . date('Ymd-His') . '.pdf', 'D');


$archivo = 'Infome_' . date('Ymd-His') . '.pdf';
$real_path      = filePath(config('app.files_tmp') . $archivo);
$public_path    = filePath(config('app.files_tmp') . $archivo, true);

$informe->Output($_SERVER['DOCUMENT_ROOT'] . '../storage/tmp/' . $archivo, 'F');
// $informe->Output('Infome_' . date('Ymd-His') . '.pdf', 'D');
//return $public_path . $archivo;

return json_encode(['data'      => [
        'real_path'      => filePath(config('app.files_tmp') . $archivo),
        'public_path'    => filePath(config('app.files_tmp') . $archivo, true)
    ]]);
