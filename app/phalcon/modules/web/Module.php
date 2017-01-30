<?php
namespace Webird\Modules\Web;

use Phalcon\DI;
use Phalcon\Loader;
use Phalcon\DiInterface;
use Webird\Module as ModuleBase;

/**
 * Module for basic web needs
 *
 */
class Module extends ModuleBase
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
    }
}
