<?php
namespace Webird\Modules\Web\Controllers;

use ZMQ;
use ZMQContext as ZMQContext;
use Webird\Mvc\Controller;
use Webird\Http\ServerSent;
use Webird\Http\ServerSent\Event;

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
     *
     */
    public function serversentAction()
    {
        $dispatcher = $this->getDI()
            ->getDispatcher();

        $option = $dispatcher->getParam(0);
        if ($option === 'messages') {
            $server = new ServerSent($this->getDI());
            $server->start();

            $count = 0;
            while ($count < 5) {
                $count++;

                $event = new Event();
                $event
                    ->setName('webird')
                    ->addData(['count' => $count])
                    ->setRetry(2);

                $server->sendEvent($event);

                sleep(1);
            }
        }
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
        $dispatcher = $this->getDI()
            ->getDispatcher();

        $option = $dispatcher->getParam(0);
        if ($option === 'api') {
            $data = [
                'this_is_data' => 'here_it_is',
            ];

            $response = $this->getDI()
                ->getResponse();

            $this->view->disable();
            $response->setJsonContent($data);
            $response->send();
        }
    }

    /**
     * Postcss processing example
     */
    public function postcssAction()
    {
    }

    /**
     * Pdf viewer
     */
    public function pdfviewerAction()
    {
    }
}
