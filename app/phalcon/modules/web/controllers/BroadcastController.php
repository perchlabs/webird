<?php
namespace Webird\Modules\Web\Controllers;

use ZMQ;
use ZMQContext;
use ZMQSocket;
use React\EventLoop\Factory as EventLoopFactory;
use React\ZMQ\Context as ReactZMQContent;
use Webird\Mvc\Controller;
use Webird\Http\ServerSent;
use Webird\Http\ServerSent\Event;

/**
 *
 */
class BroadcastController extends Controller
{
    /**
     *
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('private');
    }

    /**
     *
     */
    public function postAction()
    {
        $config = $this->getDI()
            ->getConfig();
        $request = $this->getDI()
            ->getRequest();

        $zmqPort = $config->app->zmqPort;

        $data = $request->getJsonRawBody(true);

        // Add checks here for data validity.
        $message = $data['message'];

        $context = new ZMQContext();

        $pubWebsocket = $context->getSocket(ZMQ::SOCKET_PUB);
        $pubWebsocket->connect("tcp://127.0.0.1:$zmqPort");

        $pubServersent = $context->getSocket(ZMQ::SOCKET_PUB);
        $pubServersent->connect("tcp://127.0.0.1:5554");

        usleep(50000);

        $pubWebsocket->send($message);
        $pubServersent->sendMulti(['webird', $message], ZMQ::MODE_NOBLOCK);
    }

    /**
     *
     */
    public function serversentAction()
    {
        $dispatcher = $this->getDI()
            ->getDispatcher();

        $server = new ServerSent($this->getDI());
        $server->start();

        $loop = EventLoopFactory::create();
        $context = new ReactZMQContent($loop);

        $sub = $context->getSocket(ZMQ::SOCKET_SUB);
        $sub->connect('tcp://127.0.0.1:5555');
        $sub->subscribe('webird');
        $sub->on('messages', function($msg) use ($server) {
            $event = new Event();
            $event
                ->setName('webird')
                ->addData(['message' => $msg[1]])
                ->setRetry(2);

            $server->sendEvent($event);
            // echo "Received: ". $msg[1] ." on channel: ". $msg[0] ."\n";
        });

        $loop->run();
    }
}
