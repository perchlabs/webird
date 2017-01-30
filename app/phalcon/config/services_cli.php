<?php
use Phalcon\Cli\Router;
use Phalcon\Cli\Dispatcher;
use Webird\CLI\Console;
use Webird\Session\Adapter\DatabaseReader as DatabaseSessionReader;

/**
 *
 */
$di->setShared('console', function() {
    $console = new Console($this);
    $console->registerModules([
        'cli' => ['className' => 'Webird\Modules\Cli\Module']
    ]);

    return $console;
});

/**
 *
 */
$di->setShared('router', function() {
    return new Router();
});

/**
 *
 */
$di->setShared('dispatcher', function() {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('Webird\Modules\Cli\Tasks');

    return $dispatcher;
});

/**
 *
 */
$di->set('sessionReader', function() {
    $config = $this->getConfig();
    return new DatabaseSessionReader([
        'db'          => $this->getDb(),
        'table'       => $config->session->table,
        'session_id'  => $config->session->session_id,
        'data'        => $config->session->data,
        'created_at'  => $config->session->created_at,
        'modified_at' => $config->session->modified_at,
    ]);
});
