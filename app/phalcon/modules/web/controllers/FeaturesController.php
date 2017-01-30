<?php
namespace Webird\Modules\Web\Controllers;

use ZMQ,
    ZMQContext as ZMQContext,
    Webird\Mvc\Controller;

/**
 * Controller for the Framework examples
 */
class FeaturesController extends Controller
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('private');
    }

    /**
     * Vue integration example
     */
    public function vueAction()
    {

    }

    /**
     * Vue with Vuex integration example
     */
    public function vuexAction()
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
            'when'     => time(),
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

    /**
     * Fetch API example
     */
    public function fetchAction()
    {
        $api = $this->dispatcher->getParam(0);
        if (isset($api) && $api == 'api') {
            $response = $this->getDI()->getResponse();
            $data = [
                'this_is_data' => 'here_it_is',
            ];
            $json = json_encode($data, JSON_PRETTY_PRINT);

            $response->setHeader('Content-Type', 'application/json');
            $response->setContent($json);
            $response->send();

            $this->view->disable();
        }
    }

    /**
     * Postcss processing example
     */
    public function postcssAction()
    {
    }

}
