<?php

namespace App\Kernel;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Dotenv\Dotenv;

class Kernel
{
    /**
     * @var Array
     */
    private static $config = [];
    /**
     * @var App
     */
    private static $app;

    public function __construct()
    {
        // Environment Variables
        $dotenv = Dotenv::create(__DIR__ . '/../..');
        $dotenv->load();

        // Configuration files
        $config = require __DIR__ . '/../../config/app.php';
        self::$config = $config;
    }

    /**
     * @return Array
     */
    public static function getConfig(): Array
    {
        return self::$config;
    }

    /**
     * @return App
     */
    public static function getApp(): App
    {
        return self::$app;
    }

    /**
     * Boot slim application
     * @return App
     */
    public function boot()
    {
        date_default_timezone_set(config('app.timezone'));

        @session_start();

        self::$app = new App([
            'settings' => config('app'),
            'commands' => [
                'DatesLimitTask'    => \App\Commands\DatesLimitTask::class,
                'AuctionTask'       => \App\Commands\AuctionTask::class
            ]
        ]);

        $this->loadServiceProviders();
        $this->loadUserDependencies();
        $this->loadMiddlewares();

        return self::$app;
    }

    public function loadServiceProviders()
    {
        // Register Service Providers
        container()->register(new \App\Providers\AppServiceProvider());
        container()->register(new \App\Providers\TranslationServiceProvider());
        container()->register(new \App\Providers\DatabaseServiceProvider());
        container()->register(new \App\Providers\ViewServiceProvider());
    }

    public function loadUserDependencies()
    {
        // Routes
        require rootPath() . '/routes/web.php';
        // Dependencies
        require rootPath() . '/app/Dependency/Validator.php';
        require rootPath() . '/app/Dependency/Logger.php';
    }

    public function loadMiddlewares()
    {
        app()->add(new \App\Middleware\AuthMiddleware());
        app()->add(\adrianfalleiro\SlimCLIRunner::class);
    }
}