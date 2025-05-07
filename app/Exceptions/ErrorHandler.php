<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Handlers\Error;

class ErrorHandler extends Error
{
    protected $view = 'errors/error.tpl';
    protected $status = 500;

    public function __invoke(Request $request, Response $response, $exception)
    {
        $user = user();
        
        switch ($exception->getCode()) {
            case 403:
                $this->status = $exception->getCode();
                $title = 'Oops! Ha ocurrido un error.';
                $message = 'No está autorizado para visualizar este contenido.';
                break;
            default:
                $title = 'Oops! Ha ocurrido un error.';
                $message = 'Lo estamos resolviendo, por favor regresa a la brevedad o contacta al administrador.';
                break;
        }

        $attributes = [
            'title' => $title,
            'message' => $message,
            'page' => 'error',
            'status' => $this->status,
            'description' => $exception->getMessage()
        ];

        // Loggeamos mensaje
        logger()->error($this->status . ' | ' . $exception->getMessage(), ['user' => $user ? $user->id : null]);

        if (!$request->isXhr()) {
            $response->getBody()->rewind();
            return container()->view->render($response, $this->view, $attributes)
                ->withStatus($this->status)
                ->withHeader('Content-Type', 'text/html');
        } else {
            return $response->withJson($attributes, $this->status);
        }
    }
}