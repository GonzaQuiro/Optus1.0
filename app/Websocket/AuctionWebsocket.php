<?php

namespace App\Websocket;

use \Ratchet\Server\IoServer;
use \Ratchet\Http\OriginCheck;
use \Ratchet\Http\HttpServer;
use \Ratchet\WebSocket\WsServer;
use App\Services\AuctionService;

require realpath(__DIR__ . '/../../') . '/public/index.php';
//require  __DIR__ . '/../../vendor/autoload.php';
//require __DIR__ . '/../../bootstrap/app.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new AuctionService()
        )
    ),
    8084
);

echo "\n\nSocket AuctionWebsocket, Iniciado\n";
$server->run();


/*
class AuctionWebsocket
{
    protected $server = null;

    public function __construct()
    {
        $wsService = new WsServer(new AuctionService());
        $checkedWsService = new OriginCheck($wsService);
        $checkedWsService->allowedOrigins[] = env('WEBSOCKET_DOMAIN');
        $this->server = IoServer::factory(
            new HttpServer(
                $checkedWsService
            ),
            env('WEBSOCKET_PORT')
        );
    }

    public function run()
    {
        echo "\n\nSocket AuctionWebsocket, Iniciado\n";
        $this->server->run();
    }
}

$websocket = new AuctionWebsocket();
$websocket->run();
*/