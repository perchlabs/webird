<?php
namespace Webird\Cli;

use Phalcon\DI,
    Phalcon\Loader,
    Phalcon\Mvc\ModuleDefinitionInterface as ModuleDefinitionInterface,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Webird\Mail\Mail;

/**
 * Module for CLI applications
 *
 */
class Module implements ModuleDefinitionInterface
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
        $config = DI::getDefault()->get('config');

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
