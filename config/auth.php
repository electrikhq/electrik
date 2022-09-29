<?php

return [

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => Electrik\Models\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

];
