<?php
namespace Webird\Api;

use Phalcon\DI,
    Phalcon\Loader,
    Phalcon\Mvc\View,
    Phalcon\Mvc\ModuleDefinitionInterface as ModuleDefinitionInterface,
    Webird\Mvc\ViewBase;


/**
 * Module for basic web needs
 *
 */
class Module implements ModuleDefinitionInterface
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
    }

    /**
     * Returns the module view directory for external operations
     *
     * @return string
     */
     public static function getViewsDir() { return __DIR__ . '/views/'; }


    /**
     * {@inheritdoc}
     *
     */
    public function registerAutoloaders()
    {
        $config = DI::getDefault()->get('config');

        $loader = new Loader();
        $loader->registerNamespaces([
            'Webird\Api\Controllers'  => __DIR__ . '/controllers',
            'Webird\Api\Forms'        => __DIR__ . '/forms',
            'Webird\Api'              => __DIR__ . '/library'
        ]);

        $loader->register();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Phalcon\DI  $di
     */
    public function registerServices($di)
    {
        $di->getDispatcher()->setDefaultNamespace('Webird\Api\Controllers');

        $viewsDir = self::getViewsDir();
        $di->set('view', function() use ($di, $viewsDir) {
            $view = new ViewBase();
            $view->setViewsDir($viewsDir);
            $view->setPartialsDir('../../../common/views/partials/');
            $view->setLayoutsDir('../../../common/views/layouts/');

            return $view;
        });

        // //Listen for events produced in the dispatcher using the Security plugin
        // $evManager = $di->getShared('eventsManager');
        //
        // $evManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) use ($di) {
        //
        //     switch ($exception->getCode()) {
        //
        //         case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
        //         case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
        //
        //             $dispatcher->forward([
        //                 'controller' => 'errors',
        //                 'action'     => 'show404',
        //             ]);
        //
        //             return FALSE;
        //         break;
        //     }
        // });

    }
}
