<?php
namespace Webird\Web\Controllers;

use ZMQ,
    ZMQContext as ZMQContext,

    Webird\Controllers\BaseController;
/**
 * Controller for the Framework examples
 */
class FeaturesController extends BaseController
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('private');
    }

    /**
     * Angular and Webpack integration example
     */
    public function angularAction()
    {
    }

    /**
     * Marionette and Webpack integration example
     */
    public function marionetteAction()
    {
        $zmqPort = $this->config->app->zmqPort;

        $data = [
          'category' => 'kittensCategory',
          'title'    => 'big_title',
          'article'  => 'good',
          'when'     => time()
        ];

        $context = new ZMQContext();
        $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'framework pusher');
        $socket->connect("tcp://localhost:${zmqPort}");

        $socket->send(json_encode($data));
    }



    /**
     * Ratchet and Webpack integration example
     */
    public function websocketAction()
    {

    }
}
