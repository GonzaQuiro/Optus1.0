<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Concurso;
use App\Models\Participante;
use App\Models\ParticipanteGoDocument;
use Carbon\Carbon;

class MediaController extends BaseController
{
    public function uploadFile(Request $request, Response $response)
    {
        $message = '';
        $status = 200;
        $results = [
            'initialPreview' => [],
            'initialPreviewConfig' => [],
            'initialPreviewAsData' => true
        ];

        try {
            $body = $request->getParsedBody();
            $file = !empty($request->getUploadedFiles()) ? $request->getUploadedFiles()['file'][0] : null;

            if (empty($file)) {
                $status = 422;
                $results['error'] = 'Error al recuperar los archivos.';
            } else {
                $filepath = $body['path'];
                $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
                $filename = md5(Carbon::now()->format('Y-m-d H:i:s.v')) . '.' . $extension;

                $relative_file = $filepath . DIRECTORY_SEPARATOR . $filename;
                $absolute_path = rootPath() . DIRECTORY_SEPARATOR . $filepath;

                if (!is_dir($absolute_path)) {
                    mkdir($absolute_path, 0777, true);
                }

                $file->moveTo($absolute_path . DIRECTORY_SEPARATOR . $filename);

                if ($file->getError()) {
                    $results['error'] = $file->getError();
                    $status = 422;
                } else {
                    $results['initialPreview'][] = $relative_file;
                    $results['initialPreviewConfig'][] = [
                        'key' => Carbon::now()->timestamp,
                        'caption' => $filename,
                        'size' => $file->getSize(),
                        'downloadUrl' => $relative_file,
                        'url' => route('media.file.delete'),
                        'extra' => [
                            'path' => $relative_file
                        ]
                    ];
                    $status = 200;
                }
            }

        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
            $status = 500;
        }

        return $this->json($response, $results, $status);
    }

    public function deleteFile(Request $request, Response $response)
    {
        $message = '';
        $status = 200;
        $results = [
            'initialPreview' => [],
            'initialPreviewConfig' => [],
            'initialPreviewAsData' => true
        ];

        try {
            $body = $request->getParsedBody();
            $path = rootPath() . DIRECTORY_SEPARATOR . $body['path'];
            @unlink($path);

        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, $results, $status);
    }

    public function rollbackFile(Request $request, Response $response)
    {
        $body = $request->getParsedBody();
        $success = false;
        $message = null;
        $status = 200;
        $results = [];

        try {
            $filepath = rootPath($body['path']);
            if (file_exists($filepath)) {
                @unlink($filepath);

                $success = true;
                $message = 'Archivo "' . basename($body['path']) . '" eliminado con éxito.';
            } else {
                $status = 422;
                $success = false;
                $message = 'El Archivo "' . basename($body['path']) . '" no existe.';
            }


        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message
        ], $status);
    }

    public function downloadFile(Request $request, Response $response)
    {
        $body = json_decode($request->getParsedBody()['Entity'], true);
        $path = $body['Path'];
        $type = $body['Type'];
        $id = $body['Id'];
        $success = false;
        $status = 200;
        $file_path = null;
        $message = "";


        try {
            $file_name = basename($path);
            $user = user();

            switch ($type) {
                case 'oferente':
                    $oferente = Participante::find((int) $id);
                    $file_path =
                        $oferente ?
                        $oferente->file_path . $file_name :
                        null;

                    break;
                case 'concurso':
                    $concurso = Concurso::find((int) $id);
                    if ($concurso) {
                        $file_path = $concurso->file_path . $file_name;
                    } else {
                        $file_path =
                            $user->file_path_customer .
                            $file_name;
                    }
                    break;
                case 'concurso_image':
                    $concurso = Concurso::find((int) $id);
                    $file_path = config('app.images_path') . $file_name;
                    break;
                default:
                    $file_path = $file_name;
                    break;
            }

            $file_path_absolute = rootPath() . filePath('/' . $file_path);


            if ($file_path_absolute && file_exists($file_path_absolute)) {
                $success = true;
            } else {
                $message = 'Archivo no encontrado.';
            }

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'public_path' => filePath('/' . $file_path, true)
            ]
        ], $status);
    }

    public function downloadZip(Request $request, Response $response)
    {
        $body = $request->getParsedBody();
        $success = false;
        $message = null;
        $status = 200;
        $results = [];

        try {
            $concurso = Concurso::find($body['Entity']['Id']);
            $basepath = rootPath() . filePath(config('app.files_tmp'));
            $filename = md5(Carbon::now()->format('Y-m-d H:i:s.v')) . '.zip';
            $filepath = $basepath . $filename;

            if (!is_dir($basepath)) {
                @mkdir($basepath, 0777, true);
            }

            $zip = new \ZipArchive();
            if ($zip->open($filepath, \ZipArchive::CREATE)) {
                // Archivos Concurso
                $attachments_concurso = $concurso->attachments;
                if ($attachments_concurso) {
                    foreach ($attachments_concurso as $attachment) {
                        switch ($attachment->name) {
                            case 'imagen':
                                $folder = 'concurso/imagen/';
                                break;
                            case 'pliego':
                                $folder = 'concurso/pliegos/';
                                break;
                            default:
                                $folder = '';
                                break;
                        }
                        $attachment_path = rootPath() . $attachment->path;
                        if (file_exists($attachment_path)) {
                            $zip->addFile($attachment_path, $folder . $attachment->filename);
                        }
                    }
                }

                // Archivos Oferentes
                $oferentes = $concurso->oferentes;

                if ($concurso->fecha_alta && $oferentes) {
                    foreach ($oferentes as $oferente) {
                        $file_path = filePath('/' . $oferente->file_path);
                        // TÉCNICA
                        $technical_proposal = $oferente->technical_proposal;
                        if (isset($technical_proposal->documents)) {
                            foreach ($technical_proposal->documents as $document) {
                                $folder = 'oferentes/' . $oferente->company->business_name . '/tecnica/';
                                $zip->addEmptyDir($folder);
                                $attachment_path = rootPath() . $file_path . $document->filename;
                                if (file_exists($attachment_path)) {
                                    $zip->addFile($attachment_path, $folder . $document->filename);
                                }
                            }
                        }
                        // ECONÓMICA
                        $economic_proposal = $oferente->economic_proposal;
                        if (isset($economic_proposal->documents)) {
                            foreach ($economic_proposal->documents as $document) {
                                $folder = 'oferentes/' . $oferente->company->business_name . '/economica/';
                                $zip->addEmptyDir($folder);
                                $attachment_path = rootPath() . $file_path . $document->filename;
                                if (file_exists($attachment_path)) {
                                    $zip->addFile($attachment_path, $folder . $document->filename);
                                }
                            }
                        }
                    }
                    $error = $zip->numFiles == 0 ? true : false;

                    $zip->close();

                    if (!$error) {
                        $status = 200;
                        $success = true;
                        $message = 'Archivo generado con éxito.';
                    } else {
                        $status = 500;
                        $success = false;
                        $message = 'No pudo generarse el archivo.';
                    }
                }
            } else {
                $status = 500;
                $success = false;
                $message = 'No pudo generarse el archivo.';
            }

        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success' => $success,
            'message' => $message,
            'data' => [
                'real_path' => filePath(config('app.files_tmp') . $filename),
                'public_path' => filePath(config('app.files_tmp') . $filename, true)
            ]
        ], $status);
    }
}