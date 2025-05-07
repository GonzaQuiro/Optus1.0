<?php

namespace App\Models;

use App\Models\Model;

class Step extends Model
{
    public const STEPS = [
        'customer' => [
            'convocatoria-oferentes' => 'convocatoria-oferentes',
            'chat-muro-consultas' => 'chat-muro-consultas',
            'analisis-tecnicas' => 'analisis-tecnicas',
            'analisis-ofertas' => 'analisis-ofertas',
            'evaluacion-reputacion' => 'evaluacion-reputacion',
            'informes' => 'informes',            
            
        ],
        'customer_go' => [
            'convocatoria-oferentes' => 'convocatoria-oferentes',
            'analisis-ofertas' => 'analisis-ofertas',
            'evaluacion-reputacion' => 'evaluacion-reputacion',
            'informes' => 'informes'
        ],
        'offerer' => [
            'invitacion' => 'invitacion',
            'chat-muro-consultas' => 'chat-muro-consultas',
            'tecnica' => 'tecnica',
            'economica' => 'economica',
            'analisis' => 'analisis',
            'adjudicado' => 'adjudicado'
        ],
        'offerer_go' => [
            'invitacion' => 'invitacion',
            'tecnica' => 'tecnica',
            'economica' => 'economica',
            'analisis' => 'analisis',
            'adjudicado' => 'adjudicado'
        ]
    ];

    public const STEP_TITLES = [
        'customer' => [
            'convocatoria-oferentes' => 'Convocatoria',
            'chat-muro-consultas' => 'Muro de Consultas',
            'analisis-tecnicas' => 'Técnica',
            'analisis-ofertas' => 'Económica',
            'evaluacion-reputacion' => 'Evaluación',
            'informes' => 'Informes',
            'go' => [
                'analisis-ofertas' => 'Cotización'
            ]
        ],
        'offerer' => [
            'invitacion' => 'Invitación',
            'chat-muro-consultas' => 'Muro de Consultas',
            'tecnica' => 'Técnica',
            'economica' => 'Económica',
            'analisis' => 'Análisis',
            'adjudicado' => 'Adjudicado',
            'go' => [
                'tecnica' => 'Documentación',
                'economica' => 'Cotización',
                'analisis' => 'Resultado'
            ]
        ]
    ];

    public const STEP_DESCRIPTIONS = [
        'customer' => [
            'convocatoria-oferentes' => 'Convocatoria de Proveedores',
            'chat-muro-consultas' => 'Muro de Consultas',
            'analisis-tecnicas' => 'Análisis de P. Técnica',
            'analisis-ofertas' => 'Análisis de Ofertas',
            'evaluacion-reputacion' => 'Evaluación de Reputación',
            'informes' => 'Informes',
            'go' => [
                'analisis-ofertas' => 'Cotización'
            ]
        ],
        'offerer' => [
            'invitacion' => 'Invitación al Concurso',
            'chat-muro-consultas' => 'Muro de Consultas',
            'tecnica' => 'Presentación P. Técnica',
            'economica' => 'Presentación P. Económica',
            'analisis' => 'Análisis',
            'adjudicado' => 'Adjudicado',
            'go' => [
                'tecnica' => 'Documentación',
                'economica' => 'Cotización',
                'analisis' => 'Resultado'
            ]
        ]
    ];

    public static function getTitle($type, $step, $is_go = false)
    {
        return $is_go && isset(self::STEP_TITLES[$type]['go'][$step]) ? self::STEP_TITLES[$type]['go'][$step] : self::STEP_TITLES[$type][$step];
    }

    public static function getDescription($type, $step, $is_go = false)
    {
        return $is_go && isset(self::STEP_DESCRIPTIONS[$type]['go'][$step]) ? self::STEP_DESCRIPTIONS[$type]['go'][$step] : self::STEP_DESCRIPTIONS[$type][$step];
    }

    public static function getByConcurso($concurso, $current_step)
    {
        $steps = [];

        $route_name =
            isCustomer() ? 
            'concursos.cliente.serveDetail' :
            (
                isOfferer() ? 
                'concursos.oferente.serveDetail' :
                null
            );

        $type =
            isCustomer() ? 
            'customer' :
            (
                isOfferer() ? 
                'offerer' :
                null
            );

        if ($type) {
            $step_list = self::STEPS[$concurso->is_go ? $type . '_go' : $type];
            
            $i = 0;
            foreach ($step_list as $key => $step) {
                if($step == 'chat-muro-consultas') continue;
                $i++;
                $steps[] = [
                    'number' => $i,
                    'title' => self::getTitle($type, $step, $concurso->is_go),
                    'description' => self::getDescription($type, $step, $concurso->is_go),
                    'first' => $i == 1,
                    'last' => $i == count($step_list),
                    'done' => false,
                    'current' => $current_step == $step,
                    'url' => route($route_name, ['type' => $concurso->tipo_concurso, 'id' => $concurso->id, 'step' => $step])
                ];
            }

            // Set "done" property
            foreach ($steps as &$step) {
                if ($step['current']) {
                    break;
                }
                $step['done'] = true;
            }
        }

        return $steps;
    }
}