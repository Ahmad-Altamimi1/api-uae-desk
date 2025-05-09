<?php

return [
    'middleware'       => ['api', 'auth'],
    'route_path'       => 'admin/user-activity',
    'admin_panel_path' => 'admin/dashboard',
    'delete_limit'     => 7,

    'model' => [
        'user' => "App\Models\User",
        'customer' => "App\Models\Customer"

    ],

    'log_events' => [
        'on_create'  => true,
        'on_edit'    => true,
        'on_delete'  => true,
        'on_login'   => true,
        'on_lockout' => true
    ]
];