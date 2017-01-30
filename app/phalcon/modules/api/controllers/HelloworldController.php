<?php
namespace Webird\Modules\Api\Controllers;

use Phalcon\Http\Response as Response;
use Webird\Mvc\Controller;

/**
 * Display Helloworld Api data.
 */
class HelloworldController extends Controller
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Default action.
     */
    public function indexAction()
    {
        $this->view->disable();

        $data = ['Hello', 'World'];

        $content = json_encode($data);

        $response = new Response();
        $response->setContent($content);

        return $response;
    }

}
