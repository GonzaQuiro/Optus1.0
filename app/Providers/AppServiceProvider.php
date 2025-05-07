<?php

namespace App\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use \Carbon\Carbon;
use App\Exceptions\NotFoundHandler;
use App\Exceptions\NotAllowedHandler;
use App\Exceptions\ErrorHandler;

class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $container)
    {
        $container['lang'] = function ($c) {
            setLanguage(isset($_SESSION['lang']) ? $_SESSION['lang'] : config('app.language'));
            $lang_file = require rootPath() . '/lang/' . Carbon::getLocale() . '/lang.php';
            return $lang_file;
        };

        // Override the default error handlers
        unset($container['notFoundHandler']);
        unset($container['notAllowedHandler']);
        unset($container['errorHandler']);
        unset($container['phpErrorHandler']);
        $container['notFoundHandler'] = function ($c) {
            return new NotFoundHandler($c->get('view'), function ($request, $response) use ($c) {
                return $c['response'];
            });
        };
        $container['notAllowedHandler'] = function ($c) {
            return new NotAllowedHandler($c->get('view'), function ($request, $response, $methods) use ($c) {
                return $c['response'];
            });
        };
        $container['errorHandler'] = 
        $container['phpErrorHandler'] = 
            function ($c) {
                return new ErrorHandler($c->get('view'), function ($request, $response) use ($c) {
                    return $c['response'];
                });
            };
    }
}