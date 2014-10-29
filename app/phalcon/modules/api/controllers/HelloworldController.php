<?php
namespace Webird\Api\Controllers;

use Phalcon\Http\Response as Response,
  Webird\Controllers\BaseController;

/**
 * Display Helloworld Api data.
 */
class HelloworldController extends BaseController
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
