<?php
namespace Webird\Mvc;

use Phalcon\Mvc\View;

/**
 * Console class for all CLI applications
 */
class ViewBase extends View
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


     public function render($controllerName, $actionName, $params = null)
     {
        $config = $this->getDI()->get('config');

        $this->registerEngines([
            '.volt' => 'voltService'
        ]);

        $this->setVars([
            'ENVIRONMENT' => ENVIRONMENT,
            'domain' => $config->server->domain,
            'link'   => $config->site->link
        ]);

        return parent::render($controllerName, $actionName, $params);
     }

}
