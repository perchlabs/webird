<?php
namespace Webird\Web;

use Phalcon\DI,
    Phalcon\Loader,
    Webird\Module as WbModule,
    Webird\DebugPanel;

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
            'Webird\Web\Controllers'  => __DIR__ . '/controllers',
            'Webird\Web\Forms'        => __DIR__ . '/forms',
            'Webird\Web'              => __DIR__ . '/library'
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
        $di->getDispatcher()->setDefaultNamespace('Webird\Web\Controllers');

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
