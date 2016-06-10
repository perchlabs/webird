<?php
namespace Webird\Modules\Web;

use Phalcon\DI,
    Phalcon\Loader,
    Phalcon\DiInterface,
    Webird\Module,
    Webird\DebugPanel;

/**
 * Module for basic web needs
 *
 */
class Module extends Module
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
            __NAMESPACE__ . '\\Controllers' => __DIR__ . '/controllers',
            __NAMESPACE__ . '\\Forms'       => __DIR__ . '/forms',
            __NAMESPACE__                   => __DIR__ . '/library'
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
        $di->getDispatcher()
            ->setDefaultNamespace(__NAMESPACE__ . '\\Controllers');

        $di->setShared('view', $this->getViewFunc());

        if (DEV_ENV === ENV) {
            $debugPanel = new DebugPanel($di);
        }
    }
}
