<?php

namespace App\Services;

use GuzzleHttp\Client;

class DocumentService
{
    protected $client = null;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri'  => env('API_GCG_URL'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => env('API_GCG_BEARER')
            ]
        ]);
    }

    public function getLists($cuit)
    {
        $success = true;
        $status = 200;
        $message = null;
        $drivers = null;
        $vehicles = null;

        try {
            $request = $this->client->get('contratista/' . $cuit);
            $response = json_decode($request->getBody()->getContents(), true);
            
            $drivers = isset($response['data'][0]) ? $response['data'][0]['trabajadores'] : [];
            $vehicles = isset($response['data'][0]) ? $response['data'][0]['vehiculos'] : [];
            $trailers = isset($response['data'][0]) ? $response['data'][0]['vehiculos'] : [];

            $drivers = array_map(function($driver) {
                return [
                    'id'            => $driver['id'],
                    'nombre'        => $driver['nombre'],
                    'dni'           => $driver['dni'],
                    'cuil'          => $driver['cuil'],
                    'descripcion'   => $driver['nombre'] . ' (' . $driver['dni'] . ')' 
                ];
            }, $drivers);

            $vehicles = array_map(function($vehicle) {
                return [
                    'id'            => $vehicle['id'],
                    'prestacion'    => $vehicle['prestacion'],
                    'dominio'       => $vehicle['dominio'],
                    'descripcion'   => $vehicle['prestacion'] . ' (' . $vehicle['dominio'] . ')' 
                ];
            }, $vehicles);

            $trailers = array_map(function($trailer) {
                return [
                    'id'            => $trailer['id'],
                    'prestacion'    => $trailer['prestacion'],
                    'dominio'       => $trailer['dominio'],
                    'descripcion'   => $trailer['prestacion'] . ' (' . $trailer['dominio'] . ')' 
                ];
            }, $trailers);

            if (empty($drivers) || empty($vehicles) || empty($trailers)) {
                $success = false;
                $status = 422;
                $message = isset($response['error']['message']) ? $response['error']['message'] : $response['error'];
            }
        } catch(\Exception $e) {
            $success = false;
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
            $message = $e->getMessage();
        }

        return json_encode([
            'data'  => [
                'success'   => $success,
                'message'   => $message,
                'drivers'   => $drivers,
                'vehicles'  => $vehicles,
                'trailers'  => $trailers
            ],
            'status'    => $status
        ]);
    }

    public function getDocumentation($concurso, $driver_selected, $vehicle_selected, $trailer_selected)
    {
        $success = true;
        $status = 200;
        $message = null;
        $driver = null;
        $vehicle = null;
        $trailer = null;

        try {
            $drivers = [
                'id'            => [$driver_selected],
                'documentos'    => $concurso->go->driver_gcg_documents->pluck('gcg_code')
            ];

            $vehicles = [
                'id'            => [$vehicle_selected],
                'documentos'    => $concurso->go->vehicle_gcg_documents->pluck('gcg_code')
            ];

            $trailers = [
                'id'            => [$trailer_selected],
                'documentos'    => $concurso->go->trailer_gcg_documents->pluck('gcg_code')
            ];

            $parameters = [
                'fecha'         => $concurso->fecha_alta->format('Y-m-d'),
                'trabajadores'  => $drivers,
                'vehiculos'     => 
                    $vehicle_selected && $trailer_selected ? [$vehicles, $trailers] : 
                    (
                        $vehicle_selected ? $vehicles : 
                        (
                            $trailer_selected ? $trailers : null
                        )
                    )
            ];

            $request = $this->client->post('recursos', [
                'body' => json_encode($parameters)
            ]);

            $response = json_decode($request->getBody()->getContents(), true);
            $driver = isset($response['trabajadores'][0]) ? $response['trabajadores'][0] : null;
            $vehicle = isset($response['vehiculos'][0]) ? $response['vehiculos'][0] : null;
            $trailer = isset($response['vehiculos'][1]) ? $response['vehiculos'][1] : null;

            if (empty($driver) || empty($vehicle)) {
                $success = false;
                $status = 422;
                $message = isset($response['error']['message']) ? $response['error']['message'] : $response['error'];
            }
        } catch(\Exception $e) {
            $success = false;
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
            $message = $e->getMessage();
        }

        return json_encode([
            'data'  => [
                'success'   => $success,
                'message'   => $message,
                'driver'    => $driver,
                'vehicle'   => $vehicle,
                'trailer'   => $trailer
            ],
            'status'    => $status
        ]);
    }
}