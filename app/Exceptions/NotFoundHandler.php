<?php

namespace App\Exceptions;

use Slim\Handlers\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class NotFoundHandler extends NotFound
{
    protected $view = 'errors/error.tpl';
    protected $status = 404;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) 
    {
        $user = user();

        $message = 'Puedes seguir buscando o retornar a la Home.';
        $attributes = [
            'title'     => 'Oops! La página que buscas no existe.',
            'message'   => $message,
            'page'      => 'error',
            'status'    => $this->status
        ];

        // Loggeamos mensaje
        logger()->error($this->status . ' | ' . $message, ['user' => $user ? $user->id : null]);

        if (!$request->isXhr()) {
            return container()->view->render($response, $this->view, $attributes)
                ->withStatus($this->status);
        } else {
            return $response->withJson($attributes, $this->status);
        }
    }
}