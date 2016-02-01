<?php
namespace Webird\Admin;

use Phalcon\DI,
    Phalcon\Loader,
    Phalcon\DiInterface,
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
    public function registerAutoloaders(DiInterface $di = null)
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
    public function registerServices(DiInterface $di = null)
    {
        $di->getDispatcher()->setDefaultNamespace('Webird\Admin\Controllers');

        $di->setShared('view', $this->getViewFunc($di));

        if (DEV_ENV === ENV) {
            $debugPanel = new DebugPanel($di);
        }

    }
}
