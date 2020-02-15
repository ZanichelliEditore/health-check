<?php

return [
    /**
     * Delete (or set to null) every configuration that is not necessary
     */
    /**
     * //INFO
     * 1. Use db section to define all database connections to check,
     *    an array element for every connection you have.
     * 2. Filesystem section includes s3 check (an element for every possible usage) 
     *    and local filesystem that comprehends storage size check.
     *    Define your own min size to evaluate a safe situation for your application.
     * 
     * Restore the code below to use default package configuration.
     */

    /* 'db' => [
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
        'local' => [
            'disk_name' => 'local',
            'volume_path' => '/var/www',
            'free_size_limit' => 1000 // storage as Megabyte
        ]
    ] */
];
