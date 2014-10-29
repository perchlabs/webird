<?php
namespace Webird\Api\Controllers;

use Webird\Controllers\RESTController;

/**
 * Display the default index page.
 */
class IndexController extends RESTController
{
    /**
     * Default action.
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Default action.
     */
    public function listAction()
    {
        $results = ['abc' => 123];

        $this->initResponse();
        $this->setPayload($results);

        return $this->render();
    }

}
