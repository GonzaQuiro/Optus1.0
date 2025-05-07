<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

session_start();

header("Access-Control-Allow-Origin: *");
//error_reporting(E_ALL ^ E_NOTICE);
//error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
//error_reporting(E_ALL ^ E_WARNING);
error_reporting(E_ALL ^ E_NOTICE);
ini_set('log_errors', 1);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
date_default_timezone_set('America/Argentina/Cordoba');
header('Content-Type: text/html;charset=utf-8');

require __DIR__ . '/vendor/autoload.php';

// Environment Variables
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

// Configuration files
$config = require __DIR__ . '/config/app.php';

$app = new \Slim\App([
    'settings' => $config
]);

// Get container
$container = $app->getContainer();

// Service factory for the ORM
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

?>