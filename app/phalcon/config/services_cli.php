<?php
use Phalcon\Cli\Router,
    Phalcon\Cli\Dispatcher,
    Webird\DatabaseSessionReader;

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
    $connection = $this->getDb();

    return new DatabaseSessionReader([
        'db'          => $connection,
        'unique_id'   => $config->session->unique_id,
        'db_table'    => $config->session->db_table,
        'db_id_col'   => $config->session->db_id_col,
        'db_data_col' => $config->session->db_data_col,
        'db_time_col' => $config->session->db_time_col,
        'uniqueId'    => $config->session->unique_id
    ]);
});
