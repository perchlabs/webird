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
    public static function moduleNameToDir($moduleName)
    {
        $config = DI::getDefault()
            ->getConfig();

        return $config->path->phalconDir . "modules/{$moduleName}/";
    }

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
        return $this->getModuleRootDir() . 'views/';
    }

    /**
     *
     */
    protected function getModuleRootDir()
    {
        $classParts = explode('\\', get_called_class());
        $moduleName = lcfirst($classParts[1]);

        return self::moduleNameToDir($moduleName);
    }
}
