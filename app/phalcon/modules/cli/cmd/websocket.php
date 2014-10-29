<?php
return ['service::websocketListen', [
    'title' => 'Start the websocket listener (start this through the server command).',
    'args' => [
        'required' => [],
        'optional' => []
    ],
    'opts' => [
        'p|wsport:'    => "websockets listen on port (default is {$config->app->wsPort}).",
        'z|zmqport:' => "zmq listen on port (default is {$config->app->zmqPort})."
    ]
]];
