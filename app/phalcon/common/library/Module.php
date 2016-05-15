<?php
namespace Webird;

use Phalcon\DI,
    Phalcon\Mvc\ModuleDefinitionInterface,
    Webird\Mvc\View;

/**
 * Module
 *
 */
abstract class Module implements ModuleDefinitionInterface
{
    /**
    *
    */
    public function getViewFunc()
    {
        $viewsDir = $this->getViewsDir();
        return function() use ($viewsDir) {
            $view = new View();
            $view->setDI($this);
            $view->setViewsDir($viewsDir);
            $view->setLayoutsDir('_layouts/');
            $view->setPartialsDir('_partials/');

            $view->registerEngines([
                '.volt' => 'voltService'
            ]);
            return $view;
        };
    }

    /**
     * Returns the module view directory for external operations
     *
     * @return string
     */
    protected function getViewsDir()
    {
        return $this->classNameToDir(get_called_class()) . 'views/';
    }

    /**
     *
     */
    protected function classNameToDir($moduleClass)
    {
        $classParts = explode('\\', $moduleClass);
        $moduleName = lcfirst($classParts[1]);
        $moduleDir = self::moduleNameToDir($moduleName);
        return $moduleDir;
    }

    /**
     *
     */
    public static function moduleNameToDir($moduleName)
    {
        $config = DI::getDefault()->getConfig();
        $moduleDir = $config->path->phalconDir . "modules/{$moduleName}/";
        return $moduleDir;
    }

}
