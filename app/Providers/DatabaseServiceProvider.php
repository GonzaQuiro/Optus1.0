<?php

namespace App\Providers;

use Illuminate\Events\Dispatcher;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabaseServiceProvider implements ServiceProviderInterface
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
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection(config('app.db'));
        $capsule->setAsGlobal();
        $capsule->setEventDispatcher(new Dispatcher());
        $capsule->bootEloquent();

        $container['db'] = function ($c) use ($capsule) {
            return $capsule;
        };
    }
}