<?php
namespace Webird\Web\Controllers;

use Webird\Controllers\BaseController;

/**
 * Display the terms and conditions page.
 */
class TermsController extends BaseController
{

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('public');
    }
}
