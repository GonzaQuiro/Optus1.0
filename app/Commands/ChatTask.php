<?php

namespace App\Commands;

use Psr\Container\ContainerInterface;
use Carbon\Carbon;

class ChatTask
{
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        logger('chat')->info('Ejecución de tarea chatTask.');
    }

    /**
     * AuctionTask command
     *
     * @param array $args
     * @return void
     */
    public function command($args)
    {
        // Check if process is running
        exec("ps aux | grep 'chatWebsocket' | grep -v grep | awk '{ print $2 }' | head -1", $process);
        $process_id = isset($process[0]) ? $process[0] : null;
        if ($process_id) {
            // If it is running, kill it
            shell_exec("kill $process_id");
            $message = "Reiniciando Websocket de chat Online (PID $process_id)...\n";
            logger('chat')->info($message);
            print($message);
        }
        // Launch process again
        $message = "Websocket de chat Online iniciado con éxito.\n";
        logger('chat')->info($message);
        print($message);
        shell_exec('nohup php app/Websocket/ChatWebsocket.php > /dev/null 2>&1 &');
    }
}