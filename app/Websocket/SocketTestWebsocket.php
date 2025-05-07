<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Services\SocketTestService;

require realpath(__DIR__ . '/../../') . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SocketTestService()
        )
    ),
    8084
);

echo "Iniciado\n";
$server->run();
