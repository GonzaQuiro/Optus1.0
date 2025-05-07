<?php

namespace App\Websocket;

use \Ratchet\Server\IoServer;
use \Ratchet\Http\OriginCheck;
use \Ratchet\Http\HttpServer;
use \Ratchet\WebSocket\WsServer;
use App\Services\ChatService;

require realpath(__DIR__ . '/../../') . '/public/index.php';
//require  __DIR__ . '/../../vendor/autoload.php';
//require __DIR__ . '/../../bootstrap/app.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatService()
        )
    ),
    8085
);

echo "\n\nSocket ChatWebsocket, Iniciado\n";
$server->run();