<?php

namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Carbon\Carbon;
use App\Models\Concurso;
use App\Models\User;

class AuctionServiceOld implements MessageComponentInterface
{
	protected $clients;
    protected $oferentes;
    protected $clientes;

	public function __construct() 
	{
        $this->clients = new \SplObjectStorage;
        $this->oferentes = collect();
        $this->clientes = collect();
    }

	public function onOpen(ConnectionInterface $conn) 
	{
		// Store the new connection to send messages to later
        try {
            $this->clients->attach($conn);
            $query = $conn->httpRequest->getUri()->getQuery();
            parse_str($query, $parsed_query);
            $id_concurso = is_array($parsed_query) ? $parsed_query['id_concurso'] : null;
            $id_oferente = is_array($parsed_query) ? (isset($parsed_query['id_oferente']) ? $parsed_query['id_oferente'] : null) : null;
            $id_cliente = is_array($parsed_query) ? (isset($parsed_query['id_cliente']) ? $parsed_query['id_cliente'] : null) : null;
            $listado = is_array($parsed_query) ? (isset($parsed_query['listado']) && $parsed_query['listado'] == true ? true : false) : false;
            // Agregamos el nuevo Oferente o Cliente
            if ($id_oferente) {
                $this->oferentes = $this->oferentes->push([
                    'id_recurso'    => $conn->resourceId,
                    'id_concurso'   => $id_concurso,
                    'id_oferente'   => $id_oferente
                ]);
            } elseif ($id_cliente) {
                $this->clientes = $this->clientes->push([
                    'id_recurso'    => $conn->resourceId,
                    'id_concurso'   => $id_concurso,
                    'id_cliente'    => $id_cliente,
                    'listado'       => $listado
                ]);
            }
            $oferentes_conectados = (string) $this->oferentes->where('id_concurso', $id_concurso)->count();
            foreach ($this->clients as $client) {
                // Enviamos la cantidad actualizada de personas online a los Oferentes y al Cliente.
                if ($this->oferentes
                        ->where('id_concurso', $id_concurso)
                        ->where('id_recurso', $client->resourceId)
                        ->count() > 0 ||
                    $this->clientes
                        ->where('id_concurso', $id_concurso)
                        ->where('id_recurso', $client->resourceId)
                        ->where('listado', false)
                        ->count() > 0) {

                    $client->send(json_encode(['conectados' => $oferentes_conectados]));
                }
                // ...también se lo enviamos a los Clientes que están visualizando el listado.
                if ($this->clientes
                        ->where('id_recurso', $client->resourceId)
                        ->where('listado', true)
                        ->count() > 0) {

                    $client->send(json_encode($this->getConectadosByCliente($id_cliente)));
                }
            }
            // Loggeamos y Consoleamos el mensaje
            $message = 'NUEVA CONEXIÓN: ' .
                ($id_oferente ? "Oferente {$id_oferente}" : "Cliente {$id_cliente}") .
                (!$id_concurso ? " en listado." : " en Concurso {$id_concurso}.") .
                "\n";

            logger('auction')->info($message);
            echo Carbon::now()->format('Y-m-d H:i:s') . ": " . $message;
        } catch (\Exception $exception){
            $message = $exception->getMessage();
            logger('auction')->error($message);
            echo $message;
        }

    }

    public function onMessage(ConnectionInterface $from, $msg) 
    {
        $attributes = explode(',', $msg);
        $id_concurso = $attributes[0];
        $id_oferente = $attributes[1];
        $producto = isset($attributes[2]) ? $attributes[2] : null;
        $accion = isset($attributes[3]) ? $attributes[3] : null;
        $additional_time = isset($attributes[4]) && $attributes[4] > 0 ? $attributes[4] : null;

        $concurso = Concurso::find($id_concurso);

        // Recorremos los Oferentes y Clientes...
        foreach ($this->clients as $client) {
            $output = [];
            // ... Pero solo aquellos que pertenecen al concurso en cuestión
            if ($this->oferentes->where('id_concurso', $id_concurso)->where('id_recurso', $client->resourceId)->count() > 0) {
                // Actualización de datos de la subasta.
                $output = array_merge($output, $concurso->getSubastaOutput());

                // Mensajes para mostrar en el toastr de todos menos del oferente en cuestión.
                if ($client->resourceId != $from) {
                    switch ($accion) {
                        case 'anular':
                            $output = array_merge($output,
                                ['Mensajes' => ['Un participante ha anulado una oferta del item ' . $producto . '.']]
                            );
                            break;
                        default:
                            break;
                    }
                }

                if ($additional_time) {
                    $output['Mensajes'][] = 'Se han adicionado ' . $additional_time . ' segundos a la Subasta.'; 
                    $output['TiempoAdicional'] = $additional_time;
                }
         
                $client->send(json_encode($output));

            } elseif ($this->clientes->where('id_concurso', $id_concurso)->where('id_recurso', $client->resourceId)->count() > 0) {

                // Actualización de datos de la subasta.
                if ($additional_time) {
                    $output['Mensajes'][] = 'Se han adicionado ' . $additional_time . ' segundos a la Subasta.'; 
                    $output['TiempoAdicional'] = $additional_time;
                    $output['Duracion'] = $concurso->parsed_duracion[0] . ' minutos ' . $concurso->parsed_duracion[1] . ' segundos';
                }

                $output = array_merge($output, $concurso->getSubastaOutput());
                $client->send(json_encode($output));
            }
        }

        // Loggeamos y Consoleamos el mensaje
        $message = "Oferente %d en Concurso %d ha %s una oferta para el item %s\n";
        switch ($accion) {
            case 'anular':
                $message = sprintf($message, $id_oferente, $id_concurso, 'anulado', $producto); 
                break;
            case 'cotizar':
                $message = sprintf($message, $id_oferente, $id_concurso, 'cotizado', $producto); 
                break;
        }

        $message = 'ACCIÓN: ' . $message;
        logger('auction')->info($message);
        echo Carbon::now()->format('Y-m-d H:i:s') . ': ' . $message;
    }

    public function onClose(ConnectionInterface $conn)
    {
        $isCliente = $this->clientes->where('id_recurso', $conn->resourceId)->count() > 0 ? true : false;

        // Obtengo los IDs a partir del ID de Conexión.
        $id_oferente = $this->oferentes->where('id_recurso', $conn->resourceId)->count() > 0 ? $this->oferentes->where('id_recurso', $conn->resourceId)->first()['id_oferente'] : null;
        $id_cliente = $this->clientes->where('id_recurso', $conn->resourceId)->count() > 0 ? $this->clientes->where('id_recurso', $conn->resourceId)->first()['id_cliente'] : null;
        $id_concurso = $isCliente ? $this->clientes->where('id_recurso', $conn->resourceId)->first()['id_concurso'] : $this->oferentes->where('id_recurso', $conn->resourceId)->first()['id_concurso'];

        // Eliminar conexión.
        $this->clients->detach($conn);
        
        // Actualizar collections
        $this->oferentes = $this->oferentes->filter(
            function ($oferente) use ($conn) {
                return $oferente['id_recurso'] != $conn->resourceId;
            }
        );
        $this->clientes = $this->clientes->filter(
            function ($cliente) use ($conn) {
                return $cliente['id_recurso'] != $conn->resourceId;
            }
        );

        // Cantidad de Oferentes conectados
        $oferentes_conectados = (string) $this->oferentes->where('id_concurso', $id_concurso)->count();

        foreach ($this->clients as $client) {
            // Enviamos la cantidad actualizada de personas online a los Oferentes y al Cliente.
            if ($this->oferentes
                    ->where('id_concurso', $id_concurso)
                    ->where('id_recurso', $client->resourceId)
                    ->count() > 0 || 
                $this->clientes
                    ->where('id_concurso', $id_concurso)
                    ->where('id_recurso', $client->resourceId)
                    ->where('listado', false)
                    ->count() > 0) {

                $client->send(json_encode(['conectados' => $oferentes_conectados]));
            }
        }

        // Loggeamos y Consoleamos el mensaje
        $message = 'CONEXIÓN TERMINADA: ' . 
            ($id_oferente ? "Oferente {$id_oferente}" : "Cliente {$id_cliente}") . 
            (!$id_concurso ? " ha salido del listado." : " ha salido del Concurso {$id_concurso}.") . 
            "\n";
        logger('auction')->info($message);
        echo Carbon::now()->format('Y-m-d H:i:s') . ": " . $message;
    }

    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
        $message = "Ha ocurrido un error: {$e->getMessage()}\n";
        logger('auction')->error($message);
    	echo $message;

        $conn->close();
    }

    protected function getConectadosByCliente($id_cliente)
    {
        $result = ['conectadosPorConcurso'];

        $clientes = $this->clientes
            ->where('id_cliente', $id_cliente)
            ->where('listado', true);

        if ($clientes->count() > 0) {
            foreach ($clientes as $cliente) {
                $user = User::find((int) $cliente['id_cliente']);
                $concursos = $user->concursos_creados->where('is_online', true);

                foreach ($concursos as $concurso) {
                    if ($concurso->countdown) {
                        $result['conectadosPorConcurso'][] = [
                            'id_concurso'   => $concurso->id,
                            'conectados'    => $this->oferentes->where('id_concurso', $concurso->id)->count()
                        ];
                    }
                }
            }
        }

        return $result;
    }
}