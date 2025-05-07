<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Category;
use App\Models\Area;

class AreaController extends BaseController
{
    public function serveList(Request $request, Response $response, $params)
    {
        return $this->render($response, 'rubros/list.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'listado-rubros',
            'title'     => 'Rubros Listado'
        ]);
    }

    public function serveEdit(Request $request, Response $response, $params)
    {
        $category = Category::find((int) $params['id']);
        abort_if($request, $response, !$category, true, 404);

        return $this->render($response, 'rubros/edit.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'edicion-rubro',
            'id'        => $params['id'],
            'title'     => 'Rubro Edición'
        ]);
    }

    public function serveCreate(Request $request, Response $response, $params)
    {
        return $this->render($response, 'rubros/edit.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'nuevo-rubro',
            'id'        => $params['id'],
            'title'     => 'Nuevo Rubro'
        ]);
    }

    public function serveListSubRubro(Request $request, Response $response, $params)
    {
        $category = Category::find((int) $params['parent_id']);
        abort_if($request, $response, !$category, true, 404);

        return $this->render($response, 'rubros/sub-list.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'listado-subrubros',
            'title'     => 'Sub-Rubros Listado'
        ]);
    }

    public function serveEditSubRubro(Request $request, Response $response, $params)
    {
        $category = Category::find((int) $params['parent_id']);
        abort_if($request, $response, !$category, true, 404);
        $area = $category->areas->where('id', (int) $params['id'])->first();
        abort_if($request, $response, !$area, true, 404);

        return $this->render($response, 'rubros/sub-edit.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'edicion-subrubro',
            'id'        => $params['id'],
            'title'     => 'Sub-Rubro Edición'
        ]);
    }

    public function serveCreateSubRubro(Request $request, Response $response, $params)
    {
        return $this->render($response, 'rubros/sub-edit.tpl', [
            'page'      => 'configuraciones',
            'accion'    => 'nuevo-subrubro',
            'id'        => $params['id'],
            'title'     => 'Nuevo Sub-Rubro'
        ]);
    }

    public function list(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $categories = Category::all();

            foreach ($categories as $category) {
                array_push($list, [
                    'Id'            => $category->id,
                    'Nombre'        => $category->name,
                    'NumSubrubros'  => $category->areas->count()
                ]);
            }

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Rubros', 'url' => null]
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
            $category = $creation ? null : Category::find((int) $params['id']);
            $action_description = $creation ? 'Nuevo' : 'Edición';

            $list = array_merge($list, [
                'Id'        => $creation ? null : $category->id,
                'Nombre'    => $creation ? null : $category->name
            ]);

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Rubros', 'url' => route('rubros.serveList')],
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

    public function listSubRubro(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;

        try {
            $category = Category::find((int) $params['parent_id']);

            $list = [
                'CategoryId'    => $category->id,
                'CategoryName'  => $category->name,
                'SubRubros'     => []
            ];

            foreach ($category->areas as $area) {
                array_push($list['SubRubros'], [
                    'Id'            => $area->id,
                    'Nombre'        => $area->name,
                ]);
            }

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Rubros', 'url' => route('rubros.serveList')],
                ['description' => 'Sub Rubros para "' . $category->name . '"', 'url' => null]
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

    public function editOrCreateSubRubro(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $creation = !isset($params['id']);
            $category = Category::find((int) $params['parent_id']);
            $area = $creation ? null : $category->areas->where('id', (int) $params['id'])->first();
            $action_description = $creation ? 'Nuevo' : 'Edición';

            $list = array_merge($list, [
                'Id'            => $creation ? null : $area->id,
                'Nombre'        => $creation ? null : $area->name,
                'CategoryId'    => $category->id
            ]);

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Rubros', 'url' => route('rubros.serveList')],
                ['description' => 'Sub Rubros para "' . $category->name . '"', 'url' => route('subrubros.serveList', ['parent_id' => $category->id])],
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

            $validation = $this->validate($body, $fields, true);
            if ($validation->fails()) {
                $success = false;
                $status = 422;
                $message = $validation->errors()->first();
            } else {
                if ($creation) {
                    $category = new Category($fields);
                    $category->save();
                } else {
                    $category = Category::find((int) $params['id']);
                    $category->update($fields);
                }

                $connection->commit();
                
                $redirect_url = route('rubros.serveList');
                $success = true;
                $message = 'Rubro guardado con éxito.';
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

    public function delete(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];
        $redirect_url = null;

        try {
            $category = Category::find((int) $params['id']);

            foreach ($category->areas as $area) {
                $area->delete();
            }

            $category->delete();

            $redirect_url = route('rubros.serveList');
            $success = true;
            $message = 'Rubro eliminado con éxito.';

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

    public function storeSubRubro(Request $request, Response $response, $params)
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
            $category = Category::find((int) $params['parent_id']);
            $creation = !isset($params['id']);

            $fields = [
                'name'          => $body->Nombre,
                'category_id'   => $category->id
            ];

            $validation = $this->validate($body, $fields);
            if ($validation->fails()) {
                $success = false;
                $status = 422;
                $message = $validation->errors()->first();
            } else {
                if ($creation) {
                    $area = new Area($fields);
                    $area->save();
                } else {
                    $area = $category->areas->where('id', (int) $params['id'])->first();
                    $area->update($fields);
                }
                
                $connection->commit();

                $redirect_url = route('subrubros.serveList', ['parent_id' => $category->id]);
                $success = true;
                $message = 'Sub-Rubro guardado con éxito.';
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

    public function deleteSubrubro(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];
        $redirect_url = null;

        try {
            $category = Category::find((int) $params['parent_id']);
            $area = $category->areas->where('id', (int) $params['id'])->first();

            $area->delete();

            $redirect_url = route('subrubros.serveList', ['parent_id' => $category->id]);
            $success = true;
            $message = 'Sub Rubro eliminado con éxito.';

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

    private function validate($body, $fields, $is_category = false)
    {
        $conditional_rules = [];
        $common_rules = [];

        if ($is_category) {
            $conditional_rules = array_merge($conditional_rules, [
                'name'  => 'required|string|min:2|max:50|unique:categories,name,' . $body->Id . ',id',
            ]);
        } else {
            $conditional_rules = array_merge($conditional_rules, [
                'category_id'   => 'required|exists:categories,id',
                'name'          => 'required|string|min:2|max:50|unique:areas,name,' . $body->Id . ',id',
            ]);
        }

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules)
        );
    }
}