<?php
namespace Webird\Admin\Controllers;

use Webird\Controllers\BaseController;

/**
 * Display the default index page.
 */
class IndexController extends BaseController
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
