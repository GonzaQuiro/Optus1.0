<?php

namespace App\Http\Controllers;

use \Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Carbon\Carbon;
use App\Models\User;

class BaseController
{
    protected $container;
    protected $default_data;

    protected const EXCLUDED_ROUTES = [
        'login',
        'login.send',
        'login.lg',
        'login.tlc',
        'login.callback',
        'sendRecover',
        'serverReset'
    ];

    public function __construct(ContainerInterface $container) 
    {
        $this->container = $container;
        $this->setDefaultData();
    }

    public function setDefaultData()
    {
        $this->default_data = [
            'HOST'          => env('APP_SITE_URL'),
            'logo'          => asset('/global/img/logo-small.png', true),
            /**
             * ASIGNA VARIABLES DE IDIOMA A TEMPLATES / CONFIGURACIONES COMUNES
             */
            'l'                 => $this->container->get('lang'),
            'fechaActual'       => ucfirst(Carbon::now()->formatLocalized('%B %d, %Y')),
            'is_admin'          => (int) isAdmin(),
            'is_super_admin'    => (int) isSuperAdmin()
        ];

        if (!empty($_SESSION)) {
            $this->default_data = array_merge($this->default_data, [
                'LANG'  => $_SESSION['lang']
            ]);
        }
    }

    public function render(Response $response, $template = null, $parameters = [])
    {
        return $this->container->view->render($response, ($template ?? 'index.tpl'), array_merge($parameters, $this->default_data));
    }

    public function fetch($template = null, $parameters = [])
    {
        return $this->container->view->fetch($template, array_merge($parameters, $this->default_data));
    }

    public function json(Response $response, $parameters = [], $status = null)
    {
        return $response->withJson($parameters, $status);
    }

    public function redirect(Response $response, $url, $status = null)
    {
        return $response->withRedirect($url, $status);
    }
}