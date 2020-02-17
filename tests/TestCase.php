<?php

namespace Zanichelli\HealthCheck\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Zanichelli\HealthCheck\Providers\HealthCheckServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * add the package provider
     *
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [HealthCheckServiceProvider::class];
    }

}
