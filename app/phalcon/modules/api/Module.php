<?php
namespace Webird\Api;

use Phalcon\DI,
    Phalcon\Loader,
    Webird\Module as WbModule,
    Webird\Mvc\View;


/**
 * Module for basic web needs
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

        $di->setShared('view', self::getViewFunc($di));

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
