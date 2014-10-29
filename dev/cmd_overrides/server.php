<?php
$help = <<<HELPMSG
* PHP Ratchet websocket server on port {$config->app->wsPort}
* ZMQ server on port {$config->app->zmqPort}
* Node.js/Gulp Webpack build environment server on port {$config->dev->webpackPort}
HELPMSG;

return ['dev::server', [
    'title' => "Start the dev (development) server processes",
    'help' => $help,
    'args' => [
        'required' => [],
        'optional' => []
    ],
    'opts' => []
]];
