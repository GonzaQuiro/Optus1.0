<?php

namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class SocketTestService implements MessageComponentInterface {

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);

        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $parsed_query);
        $id_concurso = is_array($parsed_query) ? (isset($parsed_query['id_concurso']) ? $parsed_query['id_concurso'] : 0) : 0;
        echo "Nueva conexion!, Id = ({$conn->resourceId}), Id curso = ({$id_concurso})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ( $this->clients as $client ) {
            if ( $from->resourceId == $client->resourceId ) 
                continue;
            $client->send( "Cliente Id $from->resourceId envia mensaje '$msg'" );
            echo "Cliente id $from->resourceId envia mensaje ( $msg )\n";
        }
    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}