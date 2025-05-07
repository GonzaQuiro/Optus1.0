<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends BaseController
{
    public function serveHome(Request $request, Response $response)
    {
        return $this->render($response, 'home/home.tpl', [
            'page'  => 'home',
            'tipo'  => 'detalle',
            'title' => ''
        ]);
    }
}