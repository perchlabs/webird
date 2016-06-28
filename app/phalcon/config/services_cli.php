<?php
use Phalcon\Cli\Router,
    Phalcon\Cli\Dispatcher,
    Webird\Session\Adapter\DatabaseReader as DatabaseSessionReader;

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
    return new Dispatcher();
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
