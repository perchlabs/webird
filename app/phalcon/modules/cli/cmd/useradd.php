<?php
return ['user::create', [
    'title' => 'Add a user with a permission role.',
    'args' => [
        'required' => ['email', 'role'],
        'optional' => []
    ],
    'opts' => [
        'p|password:' => 'set user password (otherwise it will need to be on first login).',
        'a|activate' => 'activate',
        'E|send-email?' => 'send email confirmation with optional message'
    ]
]];
