<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Catalogo;

class CatalogoController extends BaseController
{
    public function serveList(Request $request, Response $response, $params)
    {
        return $this->render($response, 'catalogos/list.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'listado-catalogos',
            'title'     => 'Categorías de Materiales'
        ]);
    }

    public function serveEdit(Request $request, Response $response, $params)
    {
        $catalogo = Catalogo::withTrashed()->find((int) $params['id']);
        abort_if($request, $response, !$catalogo, true, 404);

        return $this->render($response, 'catalogos/edit.tpl', [
            'page'      => 'configuraciones',
            //'accion'    => 'edicion-catalogo',
            'accion'    => 'listado-catalogos',
            'id'        => $params['id'],
            'title'     => 'Edición Categoría de Material'
        ]);
    }

    public function serveCreate(Request $request, Response $response, $params)
    {
        return $this->render($response, 'catalogos/edit.tpl', [
            'page'      => 'configuraciones',
            //'accion'    => 'nueva-catalogo',
            'accion'    => 'listado-catalogos',
            'title'     => 'Nueva Categoría de Material'
        ]);
    }

    public function list(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        // Breadcrumbs
        $breadcrumbs = [
            ['description' => 'Categorías de Materiales', 'url' => null]
        ];
        

        try {
            $catalogos = Catalogo::withTrashed()->get();

            foreach ($catalogos as $catalogo) {
                array_push($list, [
                    'Id'        => $catalogo->id,
                    'Nombre'    => $catalogo->name,
                    'Eliminado' => $catalogo->trashed()
                ]);
            }

            $success = true;

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 501);
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
            $catalogo = $creation ? null : Catalogo::withTrashed()->find((int) $params['id']);
            $action_description = $creation ? 'Nuevo' : 'Edición';

            $list = array_merge($list, [
                'Id'        => $creation ? null : $catalogo->id,
                'Nombre'    => $creation ? null : $catalogo->name,
                'Eliminado' => $creation ? false : $catalogo->trashed()
            ]);

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Categorías de Materiales', 'url' => route('catalogos.serveList')],
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
                    $catalogo = new Catalogo($fields);
                    $catalogo->save();
                } else {
                    $catalogo = Catalogo::withTrashed()->find((int) $params['id']);
                    $catalogo->update($fields);
                }

                // Verificar si se eliminó o reactivó.
                $catalogo->refresh();
                if ($catalogo->trashed() != $body->Eliminado) {
                    if ($body->Eliminado) {
                        $catalogo->delete();
                    } else {
                        $catalogo->restore();
                    }
                }

                $connection->commit();
                
                $redirect_url = route('catalogos.serveList');
                $success = true;
                $message = 'Categoría de Material guardada con éxito.';
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
            $catalogo = Catalogo::withTrashed()->find((int) $params['id']);

            if ($catalogo->trashed()) {
                $catalogo->restore();
                $action_description = 'Restaurada';
            } else {
                $catalogo->delete();
                $action_description = 'Eliminada';
            }

            $redirect_url = route('catalogos.serveList');
            $success = true;
            $message = 'Categoría de Material ' . $action_description .  ' con éxito.';

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
            'name'  => 'required|string|min:2|max:50|unique:catalogcategories,name,' . $body->Id . ',id',
        ];

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules)
        );
    }
}