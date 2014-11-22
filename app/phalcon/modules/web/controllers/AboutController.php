<?php
namespace Webird\Web\Controllers;

use Webird\Mvc\Controller;

/**
 * Display the "About" page.
 */
class AboutController extends Controller
{

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('public');
    }
}
