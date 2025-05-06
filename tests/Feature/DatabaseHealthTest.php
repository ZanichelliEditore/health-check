<?php

namespace Zanichelli\HealthCheck\Tests\Feature;

use Mockery as m;
use Illuminate\Support\Facades\DB;
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
        $app['config']->set('healthcheck', [
            'db' => [['connection' => 'mysql']]
        ]);
    }

    public function testCheckDatabaseFail()
    {
        $response = $this->call('GET', 'api/health');
        $response->assertStatus(503)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::DATABASE . '/mysql',
                    'available' => false,
                    'message' => trans('healthcheck::messages.DatabaseConnectionNotAvailable'),
                    'metadata' => []
                ]]
            ]);
    }


    public function testCheckDatabaseSuccess()
    {
        DB::shouldReceive('connection')->once()->andReturn(
            m::mock('Illuminate\Database\Connection', function ($mock) {
                $mock->shouldReceive('getPdo')->once()->andReturn();
            })
        );

        $response = $this->call('GET', 'api/health');
        $response->assertStatus(200)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::DATABASE . '/mysql',
                    'available' => true,
                    'message' => null,
                    'metadata' => []
                ]]
            ]);
    }
}
