<?php
namespace Webird\Modules\Web\Controllers;

use Webird\Mvc\Controller;

/**
 * Display the privacy page.
 */
class PrivacyController extends Controller
{

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('public');
    }
}
