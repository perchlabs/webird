<?php
namespace Webird\Modules\Admin\Controllers;

use Webird\Mvc\Controller;

/**
 * Display the default index page.
 */
class IndexController extends Controller
{
    /**
     * Default action. Set the public layout (layouts/admin.volt)
     */
    public function initialize()
    {
        parent::initialize();
        $this->view->setTemplateBefore('admin');
    }

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
    }

}
