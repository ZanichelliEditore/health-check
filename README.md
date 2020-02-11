# Healthcheck Laravel

## Introduction

This package has the purpose to add in laravel project an api route (**_`/api/health`_**).

There are 4 possible system checks:

- Database status (`db`)
- Availability local filesystem (`localstorage`)
- Aws S3 filesystem connection (`s3`)
- Volume available space, depending on certain limit size (`freespace`)

## Installation

First [install laravel](https://laravel.com/docs/6.x) project if you don't have yet.

Then install healthcheck package using composer:

```php
cd laravel-project
composer require zanichelli/healthcheck
```

Add **HEALTHCHECKS** param in `.env` file defining check type to launch, e.g.:

```php
HEALTHCHECKS=db,localstorage,s3,freespace
```

**`Note:`** default config vaule is _`null`_

## Customization

It is possible edit package configurations:

```php
php artisan vendor:publish --tag=config #create package config file inside own config folder
php artisan vendor:publish --tag=resources #publish messages views inside resources folder
```

## Tests

The package provides its own tests, use the following command:

```php
vendor/bin/phpunit vendor/zanichelli/healthcheck
```
