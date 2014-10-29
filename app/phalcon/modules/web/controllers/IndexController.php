<?php
namespace Webird\Web\Controllers;

use Webird\Controllers\BaseController;

/**
 * Display the default index page.
 */
class IndexController extends BaseController
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function initialize()
    {
        parent::initialize();
        $this->view->setTemplateBefore('public');
    }

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
    }

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function notificationAction()
    {
    }

}
