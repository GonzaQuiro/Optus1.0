<?php

namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Carbon\Carbon;
use App\Models\Concurso;
use App\Models\User;


class ChatService implements MessageComponentInterface
{
    protected $clients;
    protected $compradores;
    protected $proveedores;
    protected $clientes;
    protected $oferentes;
    protected $usuariosListView;
    protected $usuariosChatView;
    protected $usuariosConcursotView;
    protected $chatController;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->compradores = collect();
        $this->proveedores = collect();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $parsed_query);
        $concurso_id = $parsed_query['concurso_id'];
        $user_id = $parsed_query['user_id'];
        $isClient = $parsed_query['isClient'];
        $vista = $parsed_query['vista']; // concurso, chatlist, chatview

        if ($isClient == 'true') {
            $this->compradores = $this->compradores->push([
                'id_recurso' => $conn->resourceId,
                'concurso_id' => $concurso_id,
                'user_id' => $user_id,
                'vista' => $vista
            ]);
        } else {
            $this->proveedores = $this->proveedores->push([
                'id_recurso' => $conn->resourceId,
                'concurso_id' => $concurso_id,
                'user_id' => $user_id,
                'vista' => $vista
            ]);
        }

        $usersCompradores = $this->compradores->count() > 0 ? $this->compradores->where('concurso_id', $concurso_id)->count() : 0;
        $usersProveedores = $this->proveedores->count() > 0 ? $this->proveedores->where('concurso_id', $concurso_id)->count() : 0;

        $msg = [
            'compradoresConectadosConcurso' => $usersCompradores,
            'proveedoresConectadosConcurso' => $usersProveedores,
        ];

        foreach ($this->clients as $client) {
            // Enviamos la cantidad actualizada de personas online a los Oferentes y al Cliente.
            $client->send(json_encode($msg));
        }
        // Loggeamos y Consoleamos el mensaje
        $message = "NUEVA CONEXIÓN ({$conn->resourceId}): " .
            "Cliente {$user_id}" .
            " en Concurso {$concurso_id}." .
            "\n";

        logger('chat')->info($message);
        echo Carbon::now()->format('Y-m-d H:i:s') . ": " . $message;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo Carbon::now()->format('Y-m-d H:i:s') . ": " . strval($from->resourceId);
        $data = json_decode($msg, true); // Decodificar el mensaje JSON
        $concurso_id = $data['concurso_id'];
        $accion = $data['tipo'];

        

        // mensajes que se van a enviar a todos cuando el comprador inicia una accion
        if ($accion == 'newMessageClient' || $accion == 'newMessageProvApproved' || $accion == 'newRespClient') {
            $comprador_id = $this->compradores->where('id_recurso',  $from->resourceId)->first()['user_id'];
            foreach ($this->clients as $client) {
                if (
                    $this->proveedores->where('concurso_id', $concurso_id)->where('id_recurso', $client->resourceId)->count() > 0
                ) {
                    if ($client->resourceId != $from->resourceId) {
                        $client->send(json_encode($data));
                        $message = "Comprador %d en Concurso %d ha %s\n";
                        $message = sprintf($message, $comprador_id, $concurso_id, $accion);
                        $message = 'ACCIÓN: ' . $message;
                        logger('chat')->info($message);
                        echo Carbon::now()->format('Y-m-d H:i:s') . ': ' . $message;
                    }
                }
            }
        } else if ($accion == 'newMessageProv' || $accion == 'newRespProv' ) {
            $prov_id = $this->proveedores->where('id_recurso',  $from->resourceId)->first()['user_id'];
            foreach ($this->clients as $client) {
                if (
                    $this->compradores->where('concurso_id', $concurso_id)->where('id_recurso', $client->resourceId)->count() > 0
                ) {
                    if ($client->resourceId != $from->resourceId) {
                        $client->send(json_encode($data));
                        $message = "Proveedor %d en Concurso %d ha %s\n";
                        $message = sprintf($message, $prov_id, $concurso_id, $accion);
                        $message = 'ACCIÓN: ' . $message;
                        logger('chat')->info($message);
                        echo Carbon::now()->format('Y-m-d H:i:s') . ': ' . $message;
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $isComprador = $this->compradores->where('id_recurso', $conn->resourceId)->count() > 0 ? true : false;
        $isProveedor = $this->proveedores->where('id_recurso', $conn->resourceId)->count() > 0 ? true : false;

        // Obtengo los IDs a partir del ID de Conexión.
        $user_id = $isComprador ? $this->compradores->where('id_recurso', $conn->resourceId)->first()['user_id'] : $this->proveedores->where('id_recurso', $conn->resourceId)->first()['user_id'];

        $id_concurso = $isComprador ? $this->compradores->where('id_recurso', $conn->resourceId)->first()['concurso_id'] : $this->proveedores->where('id_recurso', $conn->resourceId)->first()['concurso_id'];

        // Eliminar conexión.
        $this->clients->detach($conn);

        // Actualizar collections
        $this->proveedores = $this->proveedores->filter(
            function ($oferente) use ($conn) {
                return $oferente['id_recurso'] != $conn->resourceId;
            }
        );
        $this->compradores = $this->compradores->filter(
            function ($cliente) use ($conn) {
                return $cliente['id_recurso'] != $conn->resourceId;
            }
        );

        $usersCompradores = $this->compradores->count() > 0 ? $this->compradores->where('concurso_id', $id_concurso)->count() : 0;
        $usersProveedores = $this->proveedores->count() > 0 ? $this->proveedores->where('concurso_id', $id_concurso)->count() : 0;

        $msg = [
            'compradoresConectadosConcurso' => $usersCompradores,
            'proveedoresConectadosChatList' => $usersProveedores,
        ];

        foreach ($this->clients as $client) {
            // Enviamos la cantidad actualizada de personas online a los Oferentes y al Cliente.
            if (
                $this->proveedores
                ->where('concurso_id', $id_concurso)
                ->where('id_recurso', $client->resourceId)
                ->count() > 0 ||
                $this->compradores
                ->where('concurso_id', $id_concurso)
                ->where('id_recurso', $client->resourceId)
                ->where('listado', false)
                ->count() > 0
            ) {

                $client->send(json_encode($msg));
            }
        }

        // Loggeamos y Consoleamos el mensaje
        $message = 'CONEXIÓN TERMINADA: ' .
            ($isProveedor ? "Proveedor {$user_id}" : "Cliente {$user_id}") .
            (!$id_concurso ? " ha salido del listado." : " ha salido del Concurso {$id_concurso}.") .
            "\n";
        logger('chat')->info($message);
        echo Carbon::now()->format('Y-m-d H:i:s') . ": " . $message;
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $message = "Ha ocurrido un error: {$e->getMessage()}\n";
        logger('chat')->error($message);
        // echo $message;
        echo $e;
        $conn->close();
    }

    protected function hasNewMessage($concurso_id, $id_oferente, $id_cliente)
    {
        $has_new_messages = false;

        // $user = User::find($cliente_id);

        $concurso = Concurso::find($concurso_id);

        $mensajes =
            isCustomer() ?
            $concurso->mensajes :
            $concurso->mensajes->where('is_approved', true);

        if ($mensajes && $mensajes->count() > 0) {
            $last_message = $mensajes->last();
            $users_read = $last_message->users_read;

            $has_new_messages = $users_read->where('id', $user->id)->count() === 0;
        }

        return $has_new_messages;
    }
}