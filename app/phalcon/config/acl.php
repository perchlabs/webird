<?php

$complete = [
    'cli' => [
        'chat' => ['open']
    ],
    'api' => [
        'users' => ['list', 'count'],
        'helloworld' => ['index']
    ],
    'web' => [
        'index' => ['index', 'notification'],
        'session' => ['signup', 'signin', 'signinOauth', 'signinRedirectOauth', 'signout'],
        'userspublic' => ['confirmEmail', 'resetPassword', 'forgotPassword'],
        'about' => ['index'],
        'privacy' => ['index'],
        'terms' => ['terms'],
        'settings' => ['changePassword'],
        'features' => ['index', 'angular', 'marionette', 'websocket']
    ],
    'admin' => [
        'index' => ['index'],
        'roles' => ['index', 'search', 'edit', 'create', 'delete'],
        'permissions' => ['index'],
        'users' => ['index', 'search', 'create', 'edit', 'delete']
    ]
];

$public = [
    'web' => [
        'index' => ['index', 'notification'],
        'session' => ['signup', 'signin', 'signinOauth', 'signinRedirectOauth', 'signout'],
        'userspublic' => ['confirmEmail', 'resetPassword', 'forgotPassword'],
        'about' => ['index'],
        'privacy' => ['index'],
        'terms' => ['terms']
    ]
];


// $actionDescriptions = [
//     'index' => $translate->gettext('Access'),
//     'search' => $translate->gettext('Search'),
//     'create' => $translate->gettext('Create'),
//     'edit' => $translate->gettext('Edit'),
//     'delete' => $translate->gettext('Delete'),
//     'changePassword' => $translate->gettext('Change password')
// ];

$actionDescriptions = [];

return [
    'complete' => $complete,
    'public' => $public,
    'actionDescriptions' => $actionDescriptions
];
