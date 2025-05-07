<?php

namespace App\Websocket;

require realpath(__DIR__ . '/../../') . '/public/index.php';

use \Ratchet\Server\IoServer;
use Ratchet\Http\OriginCheck;
use \Ratchet\Http\HttpServer;
use \Ratchet\WebSocket\WsServer;
use App\Services\AuctionService;

class AuctionWebsocketOld
{
    protected $server = null;

    public function __construct()
    {
        //echo nl2br("\r\n Incio");
        $wsService = new WsServer(new AuctionService());
        $checkedWsService = new OriginCheck($wsService);
        $checkedWsService->allowedOrigins[] = env('WEBSOCKET_DOMAIN');
        // Esto sirve para lanzar el servidor que escucha las subastas mediante el puerto indicado debajo.
        $this->server = IoServer::factory(
            new HttpServer(
                $checkedWsService
            ),
            env('WEBSOCKET_PORT')
        );
    }

    public function run()
    {
        $this->server->run();
    }
}

$websocket = new AuctionWebsocket();
$websocket->run();