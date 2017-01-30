<?php

$private = [
    'cli' => [
        'chat'        => ['open'],
    ],
    'api' => [
        'users'       => ['list', 'count'],
        'helloworld'  => ['index'],
    ],
    'web' => [
        'index'       => [],
        'session'     => [],
        'userspublic' => [],
        'about'       => ['index'],
        'privacy'     => ['index'],
        'terms'       => ['terms'],
        'settings'    => ['changePassword'],
        'features'    => ['index', 'vue', 'vuex', 'websocket', 'postcss', 'fetch'],
    ],
    'admin' => [
        'index'       => ['index'],
        'roles'       => ['index', 'search', 'edit', 'create', 'delete'],
        'permissions' => ['index'],
        'users'       => ['index', 'search', 'create', 'edit', 'delete'],
    ],
];

$public = [
    'web' => [
        'index'       => ['index', 'notification'],
        'session'     => ['signup', 'signin', 'signinOauth', 'signinRedirectOauth', 'signout'],
        'userspublic' => ['confirmEmail', 'resetPassword', 'forgotPassword'],
        'about'       => ['index'],
        'privacy'     => ['index'],
        'terms'       => ['terms'],
    ],
];

return [
    'private' => $private,
    'public'  => $public,
];
