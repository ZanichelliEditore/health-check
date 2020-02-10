<?php

return [
    /*
    * Possible values: db,localstorage,s3,freespace
    * The parameters are set in .env as a string with comma separated values
    */
    'checks' => env('HEALTHCHECKS', null),
    'params' => [
        'volume_path' => '/var/www',
        'free_size_limit' => 1000000000
    ]
];
