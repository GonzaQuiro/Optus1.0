<?php

namespace App\Commands;

use Psr\Container\ContainerInterface;
use Carbon\Carbon;

class AuctionTask
{
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        logger('auction')->info('Ejecución de tarea AuctionTask.');
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
        exec("ps aux | grep 'AuctionWebsocket' | grep -v grep | awk '{ print $2 }' | head -1", $process);
        $process_id = isset($process[0]) ? $process[0] : null;
        if ($process_id) {
            // If it is running, kill it
            shell_exec("kill $process_id");
            $message = "Reiniciando Websocket de Subasta Online (PID $process_id)...\n";
            logger('auction')->info($message);
            print($message);
        }
        // Launch process again
        $message = "Websocket de Subasta Online iniciado con éxito.\n";
        logger('auction')->info($message);
        print($message);
        shell_exec('nohup php app/Websocket/AuctionWebsocket.php > /dev/null 2>&1 &');
    }
}