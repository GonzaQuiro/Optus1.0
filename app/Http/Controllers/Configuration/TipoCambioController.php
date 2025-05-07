<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\TipoCambio;

class TipoCambioController extends BaseController
{
    // Renderiza la vista del listado de tipos de cambio
    public function serveList(Request $request, Response $response, $params)
    {
        return $this->render($response, 'tipocambio/list.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'listado-tipocambio',
            'title'     => 'Tipos de Cambio'
        ]);
    }
    
    // Retorna el listado de tipos de cambio en formato JSON
    public function list(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];       

        try {
            // Obtenemos todos los registros de la tabla 'tipocambio', incluyendo los eliminados (soft deleted)
            $tipocambios = Tipocambio::all();
            foreach ($tipocambios as $tipocambio) {
                array_push($list, [
                    'Idtipocambio' => $tipocambio->idtipocambio,
                    'Dolar'        => $tipocambio->dolar,
                    'Cambio'       => $tipocambio->cambio,
                    'Moneda'       => $tipocambio->moneda
                ]);
            }            

            $success = true;
            
            $breadcrumbs = [
                ['description' => 'Tipos de Cambio', 'url' => null]
            ];

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }
        
        // Retornamos la respuesta en formato JSON
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
