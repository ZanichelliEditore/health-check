<?php

return [
    /**
     * The parameters can be set in .env as a string with comma separated values
     * 
     * Delete (or set to null) every configuration that is not necessary
     */
    /**
     * Use db section to define all database connections to check
     */
    'db' => [
        [
            'connection' => env('DB_CONNECTION', null)
        ]
    ],
    'filesystem' => [
        's3' => [
            [
                'disk_name' =>  null
            ]
        ],
        /**
         * set value as null if it's not necessary check local filesystem
         */
        'local' => [
            'volume_path' => '/var/www',
            'free_size_limit' => 1000000000
        ]
    ]
];
