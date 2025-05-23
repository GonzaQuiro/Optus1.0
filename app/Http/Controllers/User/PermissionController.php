<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\User;
use App\Models\PermissionGroup;
use App\Models\Permission;

class PermissionController extends BaseController
{
    public function serveEdit(Request $request, Response $response, $params)
    {
        define('TOKEN_SECRET_KEY', 'Optus1.0');

        $id = (int) $params['id'];
        $user = User::find($id);
        abort_if($request, $response, !$user, true, 404);

        $expectedToken = hash_hmac('sha256', $id . session_id(), TOKEN_SECRET_KEY);
        $storedToken = $_SESSION['perm_token'][$id] ?? null;

        if (!$storedToken || $storedToken !== $expectedToken) {
            return $this->json($response, [
                'success' => false,
                'message' => 'Acceso no autorizado. Token inválido.'
            ], 403);
        }

        unset($_SESSION['perm_token'][$id]);

        $data = $user->is_admin
            ? ['type' => 'admin']
            : ($user->is_customer
                ? ['type' => 'client']
                : ['type' => 'offerer']);

        return $this->render($response, 'usuarios/permissions.tpl', [
            'page' => 'usuarios',
            'accion' => 'permisos',
            'id' => $id,
            'title' => 'Permisos',
            'urlBack' => route('usuarios.serveList', $data)
        ]);
    }

    public function guardarIdPermisos(Request $request, Response $response)
    {
        define('TOKEN_SECRET_KEY', 'Optus1.0');

        $id = $request->getParsedBody()['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            return $this->json($response, [
                'success' => false,
                'message' => 'ID inválido.'
            ], 400);
        }

        $sessionId = session_id();
        $token = hash_hmac('sha256', $id . $sessionId, TOKEN_SECRET_KEY);
        $_SESSION['perm_token'][$id] = $token;

        return $this->json($response, [
            'success' => true
        ]);
    }



    public function edit(Request $request, Response $response, $params)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];

        try {
            $user = User::find((int) $params['id']);

            $list = [
                'Id' => $user->id,
                'FullName' => strtoupper($user->full_name),
                'PermissionsSelected' => $user->permissions ? $user->permissions->pluck('id') : [],
                'PermissionGroups' => [],
		'TipoUsuario' => $user->type_id

            ];

            if ($user->is_customer || $user->is_offerer) { 
                // Obtener todos los grupos de permisos y permisos, excluyendo "Edición de Usuarios Administradores"
                $allPermissionGroups = PermissionGroup::with('permissions')->get();

                foreach ($allPermissionGroups as $group) {
                    $list['PermissionGroups'][] = [
                        'id' => (string) $group->id,
                        'text' => $group->description,
                        'permissions' => $group->permissions->filter(function ($item) {
                            return $item->description !== 'Edición de Usuarios Administradores'; // Excluir permiso específico
                        })->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'text' => $item->description
                            ];
                        })
                    ];
                }
            }
            else{
                // Get all permission groups and permissions
                $allPermissionGroups = PermissionGroup::with('permissions')->get();

                foreach ($allPermissionGroups as $group) {
                    $list['PermissionGroups'][] = [
                        'id' => (string) $group->id,
                        'text' => $group->description,
                        'permissions' => $group->permissions->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'text' => $item->description
                            ];
                        })
                    ];
                }
            }
            
            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Usuarios', 'url' => null],
                ['description' => 'Permisos "' . $user->full_name . '"', 'url' => null]
            ];

        } catch (\Exception $e) {
            dd($e);
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

            $user = User::find((int) $params['id']);

            $body = json_decode($request->getParsedBody()['Data']);

            $permission_ids = [];
            foreach ($body as $group) {
                foreach ($group->permissions as $permission) {
                    if ($permission->active) {
                        $permission_ids[] = (int) $permission->id;
                    }
                }
            }
            $user->permissions()->sync($permission_ids);

            $connection->commit();
            $data = $user->is_admin ? ['type' => 'admin'] : ($user->is_customer ? ['type' => 'client'] : ['type' => 'offerer']);
            $redirect_url = route('usuarios.serveList', $data);
            $success = true;
            $message = 'Permisos guardados con éxito.';

        } catch (\Exception $e) {
            dd($e);
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
}