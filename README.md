[![Build Status](https://travis-ci.org/ZanichelliEditore/health-check.svg?branch=master)](https://travis-ci.org/ZanichelliEditore/health-check.svg?branch=master)
[![codecov](https://codecov.io/gh/ZanichelliEditore/health-check/branch/master/graph/badge.svg)](https://codecov.io/gh/ZanichelliEditore/health-check)

# Healthcheck Laravel

## Introduction

This package has the purpose to add in laravel project an api route (**_`/api/health`_**).

There are various possible system checks:

- Database status (`db`)
- Availability local filesystem (`filesystem.local`)
- Aws S3 filesystem connection (`filesystem.s3`)

## Installation

First [install laravel](https://laravel.com/docs/6.x) project if you don't have yet.

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
