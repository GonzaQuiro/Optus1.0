<?php

namespace App\Providers;

use Illuminate\Events\Dispatcher;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use App\View\Smarty;

class ViewServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers eloquent ino container and bootup
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $container A container instance
     */
    public function register(Container $container)
    {
        // Smarty View Engine
        $container['view'] = function ($c) {
            $view = new Smarty(rootPath() . config('app.templates_path'), [
                'cacheDir'      => 'path/to/cache',
                'compileDir'    => rootPath() . config('app.templates_cache_path'),
                'debugging'     => false
            ]);

            return $view;
        };
    }
}