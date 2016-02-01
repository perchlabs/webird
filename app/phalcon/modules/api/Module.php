<?php
namespace Webird\Api;

use Phalcon\DI,
    Phalcon\Loader,
    Phalcon\DiInterface,
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
    public function registerAutoloaders(DiInterface $di = null)
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
    public function registerServices(DiInterface $di = null)
    {
        $di->getDispatcher()->setDefaultNamespace('Webird\Api\Controllers');

        $di->setShared('view', $this->getViewFunc($di));

    }
}
