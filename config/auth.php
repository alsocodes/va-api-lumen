<?php

return [
    'defaults' => [
        'guard' => 'api',
        // 'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            // 'driver' => 'eloquent',
            'driver' => 'customuserprovider',
            // 'model' => \App\User::class
            'model' => \App\Credential::class,
            'table' => 'credentials'
        ]
    ]
];