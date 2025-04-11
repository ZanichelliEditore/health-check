# Healthcheck Laravel

[![Build Status](https://travis-ci.org/ZanichelliEditore/health-check.svg?branch=master)](https://travis-ci.org/ZanichelliEditore/health-check.svg?branch=master)
[![codecov](https://codecov.io/gh/ZanichelliEditore/health-check/branch/master/graph/badge.svg)](https://codecov.io/gh/ZanichelliEditore/health-check)

## Introduction

This package has the purpose to add in laravel project an api route (**_`/api/health`_**).

There are various possible system checks:

- Database status (`db`)
- Availability local filesystem (`filesystem.local`)
- Aws S3 filesystem connection (`filesystem.s3`)

## Installation

First [install laravel](https://laravel.com/docs/12.x) project if you don't have yet.

Then install healthcheck package using composer:

```php
cd laravel-project
composer require zanichelli/healthcheck
```

Follow the [template-file](template_env.md) to include param about the package.

**`Note:`** Default config vaule is _`null`_

## Customization

It is possible edit package configurations:

```php
php artisan vendor:publish --tag=config #create package config file inside own config folder
php artisan vendor:publish --tag=resources #publish messages views inside resources folder
```

Add params in config file (**`healthcheck.php`**) to add more db connections or s3 bucket connections , e.g.:

```php
    'db' => [
        [
            'connection' => env('DB_CONNECTION', null)
        ],
        [
            'connection' => env('ORACLE_CONNECTION', null)
        ],
        [
            'connection' => env('REDIS_CONNECTION', null)
        ]
    ],
```

## Testing

You can run tests from the project where the package was installed, remember to use the PHPUnit binary located in healthcheck vendor directory. 
Assuming you have installed the package in the Example project, you can run the following commands:

`docker exec -it example_app bash`

`cd vendor/zanichelli/healthcheck`

`vendor/bin/phpunit`
