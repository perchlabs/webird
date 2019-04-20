<?php
namespace Webird\Modules\Web\Controllers;

use ZMQ;
use ZMQContext as ZMQContext;
use React\EventLoop\Factory as EventLoopFactory;
use React\ZMQ\Context as ReactZMQContent;
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
        // $this->view->setTemplateBefore('private');
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
