<?php
$help = <<<HELPMSG
* PHP Ratchet websocket server on port {$config->app->wsPort}
* ZMQ server on port {$config->app->zmqPort}
HELPMSG;

return ['dist::server', [
    'title' => 'Start the dist (distribution/production) server processes.',
    'help' => $help,
    'args' => [
        'required' => [],
        'optional' => []
    ],
    'opts' => [
        'w|wsport:'    => "websockets listen on port (default is {$config->app->wsPort}).",
        'z|zmqport:' => "zmq listen on port (default is {$config->app->zmqPort})."
    ]
]];
