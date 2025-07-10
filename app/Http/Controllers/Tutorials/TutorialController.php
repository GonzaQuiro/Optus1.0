<?php

namespace App\Http\Controllers\Tutorials;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Tutorial;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TutorialController extends BaseController
{
    use SoftDeletes;

    protected function jsonResponse(Response $response, array $data, int $status = 200)
    {
        return $response->withJson($data, $status);
    }

    public function serveList(Request $request, Response $response, $params)
    {
        return $this->render($response, 'tutoriales/list.tpl', [
            'page' => 'tutoriales',
            'accion' => 'ver-tutoriales',
            'title' => 'Tutoriales'
        ]);
    }

    public function list(Request $request, Response $response)
    {
        $success = false;
        $message = null;
        $status = 200;
        $list = [];
        $tutoriales = null;
        $type_id = $_SESSION['type_id'];

        try {
            $tutoriales = Tutorial::all();

            foreach ($tutoriales as $tutorial) {
                // —————— APLICAR STR_PAD AQUÍ ——————
                // Asegura siempre 6 caracteres (llenando con '0' a la derecha)
                $permisos = str_pad($tutorial->permisos, 6, '0', STR_PAD_RIGHT);

                if (
                    $type_id == 1 || $type_id == 2 ||                            // Administrador
                    ($type_id == 3 && $permisos[0] == '1') ||                    // Cliente
                    ($type_id == 4 && $permisos[1] == '1') ||                    // Técnico
                    ($type_id == 5 && $permisos[2] == '1') ||                    // Visor
                    ($type_id == 6 && $permisos[3] == '1') ||                    // Proveedor
                    ($type_id == 7 && $permisos[4] == '1') ||                    // Evaluador
                    ($type_id == 8 && $permisos[5] == '1')                       // Supervisor
                ) {
                    array_push($list, [
                        'Id'          => $tutorial->idtutorial,
                        'Nombre'      => $tutorial->nombre,
                        'Descripcion' => $tutorial->descripcion,
                        'Link'        => $tutorial->link,
                        // Puedes enviar el string ya padded para que en front veas siempre 6 dígitos:
                        'Permisos'    => $permisos
                    ]);
                }
            }

            $success = true;

            // Breadcrumbs
            $breadcrumbs = [
                ['description' => 'Tutoriales', 'url' => null]
            ];
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode')
                ? $e->getStatusCode()
                : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data'    => [
                'list'        => $list,
                'breadcrumbs' => $breadcrumbs,
            ]
        ], $status);
    }


    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (empty($data['UserToken']) || empty($data['Nombre']) || empty($data['Descripcion']) || empty($data['Link'])) {
            return $response->withJson([
                'success' => false,
                'message' => 'Debe completar todos los campos'
            ], 400);
        }

        try {
            $tutorial = new Tutorial();
            $tutorial->nombre = $data['Nombre'];
            $tutorial->descripcion = $data['Descripcion'];
            $tutorial->link = $data['Link'];
            $tutorial->permisos = $data['Permisos'];

            $tutorial->save();

            return $response->withJson([
                'success' => true,
                'message' => 'Tutorial agregado correctamente.',
                'data' => [
                    'redirect' => '/tutoriales'
                ]
            ]);
        } catch (\Exception $e) {
            return $response->withJson([
                'success' => false,
                'message' => 'Error al agregar el tutorial: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request, Response $response)
    {
        try {
            $data = $request->getParsedBody();
            $id = $data['Id'];
            $nombre = $data['Nombre'];
            $descripcion = $data['Descripcion'];
            $link = $data['Link'];
            $permisos = $data['Permisos'];

            $tutorial = Tutorial::where('idtutorial', $id)->first();
            if (!$tutorial) {
                throw new \Exception('Tutorial no encontrado.');
            }

            $tutorial->nombre = $nombre;
            $tutorial->descripcion = $descripcion;
            $tutorial->link = $link;
            $tutorial->permisos = $permisos;
            $tutorial->save();

            return $this->jsonResponse($response, [
                'success' => true,
                'message' => 'Tutorial eliminado correctamente.',
                'data' => [
                    'redirect' => '/tutoriales'
                ]
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'success' => false,
                'message' => 'Error al eliminar el tutorial: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request, Response $response)
    {
        try {
            $data = $request->getParsedBody();
            $id = $data['Id'];

            $tutorial = Tutorial::where('idtutorial', $id)->first();
            if (!$tutorial) {
                throw new \Exception('Tutorial no encontrado.');
            }

            $tutorial->delete();

            return $this->jsonResponse($response, [
                'success' => true,
                'message' => 'Tutorial eliminado correctamente.',
                'data' => [
                    'redirect' => '/tutoriales'
                ]
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, [
                'success' => false,
                'message' => 'Error al eliminar el tutorial: ' . $e->getMessage()
            ], 500);
        }
    }
}