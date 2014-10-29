<?php
namespace Webird\Mvc;

use Phalcon\Mvc\Router as PhRouter;

class Router extends PhRouter
{
    public function addStdModule($moduleName)
    {
        $this->add("/$moduleName", [
            'module' => $moduleName,
            'controller' => 'index'
        ]);
        $this->add("/$moduleName/:controller", [
            'module' => $moduleName,
            'controller' => 1
        ]);
        $this->add("/$moduleName/:controller/:action", [
            'module' => $moduleName,
            'controller' => 1,
            'action' => 2,
        ]);
        $this->add("/$moduleName/:controller/:action/:params", [
            'module' => $moduleName,
            'controller' => 1,
            'action' => 2,
            'params' => 3
        ]);
    }
}
