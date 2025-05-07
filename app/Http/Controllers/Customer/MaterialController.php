<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Catalogo;
use App\Models\CustomerCatalogo;
use App\Models\Measurement;
use Illuminate\Validation\Rule;

class MaterialController extends BaseController
{
    public function serveList(Request $request, Response $response, $params)
    {
        return $this->render($response, 'empresas/materiales/list.tpl', [
            'page' => 'empresas',
            'accion' => 'listado-materiales',
            'title' => 'Catálogo de Materiales'
        ]);
    }

    public function serveEdit(Request $request, Response $response, $params)
    {
        $catalogo = CustomerCatalogo::withTrashed()->find((int) $params['id']);
        abort_if($request, $response, !$catalogo, true, 404);

        return $this->render($response, 'empresas/materiales/edit.tpl', [
            'page' => 'empresas',
            'accion' => 'listado-materiales',
            'id' => $params['id'],
            'title' => 'Edición de Material'
        ]);
    }

    public function serveCreate(Request $request, Response $response, $params)
    {
        return $this->render($response, 'empresas/materiales/edit.tpl', [
            'page' => 'empresas',
            'accion' => 'listado-materiales',
            'title' => 'Nuevo Material'
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
            ['description' => 'Materiales', 'url' => null]
        ];

        try {
            $user = user();
            $customercatalogos = CustomerCatalogo::withTrashed()->where('customer_id', $user->customer_company->id)->get();

            foreach ($customercatalogos as $item) {
                array_push($list, [
                    'Id' => $item->id,
                    'NombreCategoria' => $item->NombreCategoria,
                    'CodigoERP' => $item->codigo_ERP,
                    'Descripcion' => $item->description,
                    'CodigoProveedor' => $item->codigo_proveedor,
                    'Proveedor' => $item->proveedor,
                    'Eliminado' => $item->trashed()
                ]);
            }
            ;

            $listCategorias = Catalogo::getList();
            $listUnidades = Measurement::getList();

            $success = true;

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 501);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'list' => $list,
                'categorias' => $listCategorias,
                'unidades' => $listUnidades,
                'breadcrumbs' => $breadcrumbs
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
            $customercatalogo = $creation ? null : CustomerCatalogo::withTrashed()->find((int) $params['id']);
            $action_description = $creation ? 'Nuevo' : 'Edición';

            $list = array_merge($list, [
                'Id' => $creation ? null : $customercatalogo->id,
                'Categoria' => $creation ? null : $customercatalogo->catalogcategory_id,
                'Categorias' => Catalogo::getList(),
                'CodigoERP' => $creation ? null : $customercatalogo->codigo_ERP,
                'Descripcion' => $creation ? null : $customercatalogo->description,
                'DescripcionLarga' => $creation ? null : $customercatalogo->long_description,
                'TargetCost' => $creation ? null : $customercatalogo->targetcost,
                'Unidad' => $creation ? null : $customercatalogo->unidad,
                'Unidades' => Measurement::getList(),
                'CodigoProveedor' => $creation ? null : $customercatalogo->codigo_proveedor,
                'Proveedor' => $creation ? null : $customercatalogo->proveedor,
                'Eliminado' => $creation ? false : $customercatalogo->trashed()
            ]);

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Materiales', 'url' => route('materiales.serveList')],
                ['description' => $action_description, 'url' => null]
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
                'breadcrumbs' => $breadcrumbs
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

            $user = user();
            $fields = [
                'id' => $creation ? null : $params['id'],
                'customer_id' => $user->customer_company->id,
                'catalogcategory_id' => $body->Categoria,
                'codigo_ERP' => isset($body->CodigoERP) ? $body->CodigoERP : "",
                'description' => $body->Descripcion,
                'long_description' => isset($body->DescripcionLarga) ? $body->DescripcionLarga : $body->Descripcion,
                'targetcost' => isset($body->TargetCost) ? $body->TargetCost : 0,
                'unidad' => isset($body->Unidad) ? $body->Unidad : null,
                'codigo_proveedor' => isset($body->CodigoProveedor) ? $body->CodigoProveedor : "",
                'proveedor' => isset($body->Proveedor) ? $body->Proveedor : ""
            ];

            //dd($fields);

            //$validation = $this->validate($body, $fields);
            $validation = $this->validate($fields);
            if ($validation->fails()) {
                $success = false;
                $status = 422;
                $message = $validation->errors()->first();
            } else {
                if ($creation) {
                    $customercatalogo = new CustomerCatalogo($fields);
                    $customercatalogo->save();
                } else {
                    $customercatalogo = CustomerCatalogo::withTrashed()->find((int) $params['id']);
                    $customercatalogo->update($fields);
                }

                // Verificar si se eliminó o reactivó.
                $customercatalogo->refresh();
                if ($customercatalogo->trashed() != $body->Eliminado) {
                    if ($body->Eliminado) {
                        $customercatalogo->delete();
                    } else {
                        $customercatalogo->restore();
                    }
                }

                $connection->commit();

                $redirect_url = route('materiales.serveList');
                $success = true;
                $message = 'Material guardado con éxito.';
            }

        } catch (\Exception $e) {
            $connection->rollBack();
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

    public function import(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $redirect_url = null;
        $procesados = 0;
        $procesadoserrores = 0;


        try {
            $capsule = dependency('db');
            $connection = $capsule->getConnection();
            $connection->beginTransaction();

            $user = user();
            $body = json_decode($request->getParsedBody()['Data']);


            foreach ($body as $value) {

                $catalogo = Catalogo::withTrashed()->where('name', $value->Categoria)->first();
                $unidad = null;
                if (isset($value->Unidad))
                    $unidad = Measurement::withTrashed()->where('name', $value->Unidad)->first();

                if (isset($catalogo)) {
                    $fields = [
                        'id' => null,
                        'customer_id' => $user->customer_company->id,
                        'catalogcategory_id' => $catalogo->id,
                        'codigo_ERP' => isset($value->CodigoERP) ? $value->CodigoERP : "",
                        'description' => $value->Descripcion,
                        'long_description' => isset($value->DescripcionLarga) ? $value->DescripcionLarga : $value->Descripcion,
                        'targetcost' => isset($value->CostoObjetivo) ? $value->CostoObjetivo : 0,
                        'unidad' => isset($unidad->id) ? $unidad->id : null,
                        'codigo_proveedor' => isset($value->CodigoProveedor) ? $value->CodigoProveedor : "",
                        'proveedor' => isset($value->Proveedor) ? $value->Proveedor : ""
                    ];

                    $validation = $this->validate($fields);
                    if (!$validation->fails()) {
                        try {
                            $customercatalogo = new CustomerCatalogo($fields);
                            $customercatalogo->save();
                            $procesados++;
                        } catch (\Exception $e) {
                            $procesadoserrores++;
                        }
                    } else {
                        foreach ($validation->messages()->messages() as $key => $value) {
                            dd($key);
                            dd($value);
                            break;
                        }
                        $procesadoserrores++;
                    }
                }
            }

            $connection->commit();

            $redirect_url = route('materiales.serveList');
            $success = true;
            $message = 'Materiales guardados con éxito.';

        } catch (\Exception $e) {
            $connection->rollBack();
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message . ', creado(s) ' . $procesados . ' registro(s), ' . $procesadoserrores . ' con errores.',
            'data' => [
                'redirect' => $redirect_url
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
            $customercatalogos = CustomerCatalogo::withTrashed()->find((int) $params['id']);

            if ($customercatalogos->trashed()) {
                $customercatalogos->restore();
                $action_description = 'Restaurada';
            } else {
                $customercatalogos->delete();
                $action_description = 'Eliminada';
            }

            $redirect_url = route('materiales.serveList');
            $success = true;
            $message = 'Material ' . $action_description . ' con éxito.';

        } catch (\Exception $e) {
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

    private function validate($fields)
    {
        $id = $fields['id'];
        $customerId = $fields['customer_id'];
        $codigoERP = $fields['codigo_ERP'];
        $description = $fields['description'];

        $conditional_rules = [];
        $common_rules = [
            'catalogcategory_id' => 'required|numeric',
            'description' => 'required|string|min:2|max:100',
            'codigo_ERP' => [
                Rule::unique('customer_catalog')->where(function ($query) use ($customerId, $codigoERP, $description) {
                    return $query
                        ->where('customer_id', $customerId)
                        ->where('codigo_ERP', $codigoERP)
                        ->where('description', $description);
                })->ignore($id)
            ]
        ];

        return validator(
            $data = $fields,
            $rules = array_merge($common_rules, $conditional_rules)
        );
    }
}