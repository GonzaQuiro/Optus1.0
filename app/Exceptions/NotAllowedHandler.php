<?php

namespace App\Exceptions;

use Slim\Handlers\NotAllowed;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class NotAllowedHandler extends NotAllowed
{
    protected $view = 'errors/error.tpl';
    protected $status = 405;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $methods) 
    {
        $user = user();

        $message = 'El método debe ser uno de los siguientes: ' . implode(', ', $methods);
        $attributes = [
            'title'     => 'Método no permitido.',
            'message'   => $message,
            'page'      => 'error',
            'status'    => $this->status
        ];

        // Loggeamos mensaje
        logger()->error($this->status . ' | ' . $message, ['user' => $user ? $user->id : null]);

        if (!$request->isXhr()) {
            return container()->view->render($response, $this->view, $attributes)
                ->withStatus($this->status)
                ->withHeader('Allow', implode(', ', $methods))
                ->withHeader('Content-type', 'text/html');
        } else {
            return $response->withJson($attributes, $this->status);
        }
    }
}