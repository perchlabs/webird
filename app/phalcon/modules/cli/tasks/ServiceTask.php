<?php
namespace Webird\Modules\Cli\Tasks;

use ZMQ;
use ZMQContext;
use ZMQDevice;
use React\ZMQ\Context as ReactZMQContext;
use React\EventLoop\Factory as EventLoopFactory;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use React\Socket\Server as Reactor;

use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;

use Webird\CLI\Task;
use Webird\Modules\Cli\Broadcaster;

/**
 * Task for websocket
 *
 */
class ServiceTask extends Task
{
    /**
     *
     */
    public function mainAction(array $params)
    {
        echo "The default action inside of the ", CURRENT_TASK, " task is not configured\n";
    }

    /**
     *
     */
    public function serversentAction($argv)
    {
        $ctx = new ZMQContext();

        $sub = $ctx->getSocket(ZMQ::SOCKET_XSUB);
        $sub->bind("tcp://127.0.0.1:5554");
        $sub->setSockOpt(ZMQ::SOCKOPT_LINGER, 0);

        $pub = $ctx->getSocket(ZMQ::SOCKET_XPUB);
        $pub->bind("tcp://127.0.0.1:5555");
        $pub->setSockOpt(ZMQ::SOCKOPT_LINGER, 0);

        echo "The device server has been started.\n";

        $device = new ZMQDevice($sub, $pub);
        $device->run();
    }

    /**
     *
     */
    public function websocketAction($argv)
    {
        $config = $this->di->getConfig();

        $params = $this->parseArgs($argv, [
            'title' => 'Start the websocket listener (start this through the server command).',
            'args' => [
                'required' => [],
                'optional' => [],
            ],
            'opts' => [
                'p|wsport:'  => "websockets listen on port (default is {$config->app->wsPort}).",
                'z|zmqport:' => "zmq listen on port (default is {$config->app->zmqPort}).",
            ]
        ]);

        // $this->ensureRunningAsWebUser();
        $opts = $params['opts'];
        $config = $this->config;

        $wsPort = (isset($opts['wsport'])) ? $opts['wsport'] : $config->app->wsPort;
        $zmqPort = (isset($opts['zmqport'])) ? $opts['zmqport'] : $config->app->zmqPort;

        $loop = EventLoopFactory::create();

        $broadcaster = new Broadcaster();
        $broadcaster->setDI($this->getDI());

        // Listen for the web server to make a ZeroMQ push after an ajax request
        $context = new ReactZMQContext($loop);
        $sub = $context->getSocket(ZMQ::SOCKET_SUB);
        $sub->subscribe('');
        $sub->bind("tcp://127.0.0.1:$zmqPort");
        $sub->on('message', [$broadcaster, 'onPost']);

        $wsServer = new WsServer($broadcaster);
        $httpServer = new HttpServer($wsServer);
        $socket = new Reactor("0.0.0.0:$wsPort", $loop);
        $ioServer = new IoServer($httpServer, $socket, $loop);

        echo "websocket listening on port $wsPort in " . ENV . " mode\n";

        $loop->run();
    }
}
