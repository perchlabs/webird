<?php
namespace Webird\Web\Controllers;

use Webird\Controllers\BaseController;

/**
 * Display the privacy page.
 */
class PrivacyController extends BaseController
{

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('public');
    }
}
