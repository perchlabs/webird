<?php
return ['user::password', [
    'title' => 'Change the password of an existing user by email or primary key.',
    'args' => [
        'required' => ['user', 'new_password'],
        'optional' => []
    ],
    'opts' => []
]];
