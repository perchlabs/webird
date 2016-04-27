<?php
namespace Webird\Mvc;

use Phalcon\Mvc\View as PhView;

/**
 * View
 */
class View extends PhView
{
    /**
     * Constructor
     *
     * @param \Phalcon\DI   $di
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    /**
     *
     */
    public function render($controllerName, $actionName, $params = null)
    {
        $config = $this->getDI()->get('config');

        $this->setVars([
            'DEV'    => DEV,
            'TEST'   => (TEST_ENV === ENV),
            'DIST'   => (DIST_ENV === ENV),
            'domain' => $config->server->domain,
            'link'   => $config->site->link
        ]);

        return parent::render($controllerName, $actionName, $params);
    }

}
