<?php

namespace Zanichelli\HealthCheck\Tests\Feature;

use Zanichelli\HealthCheck\Http\Constants\Service;
use Zanichelli\HealthCheck\Tests\TestCase;

class DatabaseHealthTest extends TestCase
{

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('healthcheck.db', [['connection' => 'mysql']]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkDatabaseFail()
    {
        $response = $this->call('GET', 'api/health');
        $response->assertStatus(400)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::DATABASE . '/mysql',
                    'available' => false,
                    'message' => trans('healthcheck::messages.DatabaseConnectionNotAvailable'),
                    'metadata' => []
                ]]
            ]);
    }
}
