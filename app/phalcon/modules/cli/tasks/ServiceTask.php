<?php
namespace Webird\Cli\Tasks;

use ZMQ,
    PDO,
    React\ZMQ\Context as ZMQContext,
    React\EventLoop\Factory as EventLoopFactory,
    Ratchet\Server\IoServer,
    Ratchet\Http\HttpServer,
    Ratchet\WebSocket\WsServer,
    Ratchet\Session\SessionProvider,
    Symfony\Component\HttpFoundation\Session\Storage\Handler,
    Webird\Cli\TaskBase,
    Webird\Cli\Chat;

/**
 * Task for websocket
 *
 */
class ServiceTask extends TaskBase
{
    public function mainAction(array $params)
    {
        echo "The default action inside of the ", CURRENT_TASK, " task is not configured\n";
    }



    public function websocketListenAction(array $params)
    {
        // $this->ensureRunningAsWebUser();
        $opts = $params['opts'];
        $config = $this->config;

        $wsPort = (isset($opts['wsport'])) ? $opts['wsport'] : $config->app->wsPort;
        $zmqPort = (isset($opts['zmqport'])) ? $opts['zmqport'] : $config->app->zmqPort;

        $loop = EventLoopFactory::create();
        $chat = new Chat();
        $chat->setDI($this->getDI());


        // Listen for the web server to make a ZeroMQ push after an ajax request
        // $context = new ZMQContext($loop);
        // $pull = $context->getSocket(ZMQ::SOCKET_PULL);
        // $pull->bind("tcp://127.0.0.1:${zmqPort}"); // Binding to 127.0.0.1 means the only client that can connect is itself
        // $pull->on('message', [$chat, 'onUserJoin']);

        $wsServer = new WsServer($chat);

        $ioServer = IoServer::factory(
            new HttpServer($wsServer),
            $wsPort
        );

        echo "websocket listening on port $wsPort in " . ENV . " mode\n";

        $ioServer->run();
    }


}
