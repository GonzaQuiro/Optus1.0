<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Measurement;

class MeasurementController extends BaseController
{
    public function serveList(Request $request, Response $response, $params)
    {
        return $this->render($response, 'unidades/list.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'listado-unidades',
            'title'     => 'Unidades de Medida'
        ]);
    }

    public function serveEdit(Request $request, Response $response, $params)
    {
        $measurement = Measurement::withTrashed()->find((int) $params['id']);
        abort_if($request, $response, !$measurement, true, 404);

        return $this->render($response, 'unidades/edit.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'edicion-unidad',
            'id'        => $params['id'],
            'title'     => 'Edición Unidad de Medida'
        ]);
    }

    public function serveCreate(Request $request, Response $response, $params)
    {
        return $this->render($response, 'unidades/edit.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'nueva-unidad',
            'title'     => 'Nueva Unidad de Medida'
        ]);
    }

    public function list(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $measurements = Measurement::withTrashed()->get();

            foreach ($measurements as $measurement) {
                array_push($list, [
                    'Id'        => $measurement->id,
                    'Nombre'    => $measurement->name,
                    'Eliminado' => $measurement->trashed()
                ]);
            }

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Unidades de Medida', 'url' => null]
            ];

        } catch (\Exception $e) {
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

    public function editOrCreate(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $creation = !isset($params['id']);
            $measurement = $creation ? null : Measurement::withTrashed()->find((int) $params['id']);
            $action_description = $creation ? 'Nuevo' : 'Edición';

            $list = array_merge($list, [
                'Id'        => $creation ? null : $measurement->id,
                'Nombre'    => $creation ? null : $measurement->name,
                'Eliminado' => $creation ? false : $measurement->trashed()
            ]);

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Unidades de Medida', 'url' => route('unidades.serveList')],
                ['description' => $action_description, 'url' => null]
            ];

        } catch (\Exception $e) {
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

    public function store(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];
        $redirect_url = null;

        try {
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();

            $body = json_decode($request->getParsedBody()['Data']);
            $creation = !isset($params['id']);

            $fields = [
                'name'  => $body->Nombre
            ];

            $validation = $this->validate($body, $fields);
            if ($validation->fails()) {
                $success = false;
                $status = 422;
                $message = $validation->errors()->first();
            } else {
                if ($creation) {
                    $measurement = new Measurement($fields);
                    $measurement->save();
                } else {
                    $measurement = Measurement::withTrashed()->find((int) $params['id']);
                    $measurement->update($fields);
                }

                // Verificar si se eliminó o reactivó.
                $measurement->refresh();
                if ($measurement->trashed() != $body->Eliminado) {
                    if ($body->Eliminado) {
                        $measurement->delete();
                    } else {
                        $measurement->restore();
                    }
                }

                $connection->commit();
                
                $redirect_url = route('unidades.serveList');
                $success = true;
                $message = 'Unidad de Medida guardada con éxito.';
            }

        } catch (\Exception $e) {
            $connection->rollBack();
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'redirect'  => $redirect_url
            ]
        ], $status);
    }

    public function toggle(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];
        $redirect_url = null;

        try {
            $measurement = Measurement::withTrashed()->find((int) $params['id']);

            if ($measurement->trashed()) {
                $measurement->restore();
                $action_description = 'Restaurada';
            } else {
                $measurement->delete();
                $action_description = 'Eliminada';
            }

            $redirect_url = route('unidades.serveList');
            $success = true;
            $message = 'Unidad de Medida ' . $action_description .  ' con éxito.';

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message,
            'data'      => [
                'redirect'  => $redirect_url
            ]
        ], $status);
    }

    private function validate($body, $fields)
    {
        $conditional_rules = [];
        $common_rules = [
            'name'  => 'required|string|min:2|max:50|unique:measurements,name,' . $body->Id . ',id',
        ];

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules)
        );
    }
}