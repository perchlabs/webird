<?php

$complete = [
    'cli' => [
        'chat' => ['open']
    ],
    'api' => [
        'users'      => ['list', 'count'],
        'helloworld' => ['index']
    ],
    'web' => [
        'index'       => ['index', 'notification'],
        'session'     => ['signup', 'signin', 'signinOauth', 'signinRedirectOauth', 'signout'],
        'userspublic' => ['confirmEmail', 'resetPassword', 'forgotPassword'],
        'about'       => ['index'],
        'privacy'     => ['index'],
        'terms'       => ['terms'],
        'settings'    => ['changePassword'],
        'features'    => ['index', 'angular', 'marionette', 'websocket', 'postcss']
    ],
    'admin' => [
        'index'       => ['index'],
        'roles'       => ['index', 'search', 'edit', 'create', 'delete'],
        'permissions' => ['index'],
        'users'       => ['index', 'search', 'create', 'edit', 'delete']
    ]
];

$public = [
    'web' => [
        'index'       => ['index', 'notification'],
        'session'     => ['signup', 'signin', 'signinOauth', 'signinRedirectOauth', 'signout'],
        'userspublic' => ['confirmEmail', 'resetPassword', 'forgotPassword'],
        'about'       => ['index'],
        'privacy'     => ['index'],
        'terms'       => ['terms']
    ]
];

return [
    'complete' => $complete,
    'public'   => $public
];
