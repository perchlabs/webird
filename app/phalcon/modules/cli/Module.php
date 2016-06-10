<?php
namespace Webird\Modules\Cli;

use Phalcon\DI,
    Phalcon\Loader,
    Phalcon\DiInterface,
    Webird\Module;

/**
 * Module for CLI interface applications
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
            __NAMESPACE__ . '\\Tasks' => __DIR__ . '/tasks',
            __NAMESPACE__             => __DIR__ . '/library'
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

    }

}
