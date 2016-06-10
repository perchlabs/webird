<?php
namespace Webird\Modules\Web\Controllers;

use Webird\Mvc\Controller;

/**
 * Display the terms and conditions page.
 */
class TermsController extends Controller
{

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('public');
    }
}
