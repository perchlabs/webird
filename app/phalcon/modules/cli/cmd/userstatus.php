<?php
return ['user::status', [
    'title' => 'Modify or view status for a user by email or primary key.',
    'args' => [
        'required' => ['user'],
        'optional' => []
    ],
    'opts' => [
        'r|role'                  => 'Set user permission role',
        'a|active:'               => 'Set user active status',
        'b|banned:'               => 'Set user banned status. A banned user is also deactivated',
        'm|must-change-password:' => 'Set must change password status'
    ]
]];
