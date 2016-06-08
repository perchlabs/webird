<?php
namespace Webird\Web;

use Phalcon\DI,
    Phalcon\Loader,
    Phalcon\DiInterface,
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
    public function registerAutoloaders(DiInterface $di = null)
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
    public function registerServices(DiInterface $di = null)
    {
        $di->getDispatcher()->setDefaultNamespace('Webird\Web\Controllers');

        $di->setShared('view', $this->getViewFunc());

        if (DEV_ENV === ENV) {
            $debugPanel = new DebugPanel($di);
        }
    }
}
