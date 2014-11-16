<?php
namespace Webird\Admin;

use Phalcon\DI,
    Phalcon\Loader,
    Webird\Module as WbModule,
    Webird\DebugPanel;

/**
 * Module for system administration
 *
 */
class Module extends WbModule
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     *
     */
    public function registerAutoloaders()
    {
        $loader = new Loader();
        $loader->registerNamespaces([
            'Webird\Admin\Controllers'  => __DIR__ . '/controllers',
            'Webird\Admin\Forms'        => __DIR__ . '/forms',
            'Webird\Admin'              => __DIR__ . '/library'
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
        $di->getDispatcher()->setDefaultNamespace('Webird\Admin\Controllers');

        $di->setShared('view', self::getViewFunc($di));

        if (DEV_ENV === ENV) {
            $debugPanel = new DebugPanel($di);
        }


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
