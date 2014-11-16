<?php
namespace Webird\Cli;

use Phalcon\DI,
    Phalcon\Loader,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Webird\Module as WbModule;

/**
 * Module for CLI interface applications
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
            'Webird\Cli\Tasks'    => __DIR__ . '/tasks',
            'Webird\Cli'          => __DIR__ . '/library'
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

    }

}
