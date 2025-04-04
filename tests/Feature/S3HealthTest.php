<?php

namespace Zanichelli\HealthCheck\Tests\Feature;

use Zanichelli\HealthCheck\Tests\TestCase;
use Zanichelli\HealthCheck\Tests\Unit\HealthTest;
use Zanichelli\HealthCheck\Http\Constants\Service;

class S3HealthTest extends TestCase
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
            'filesystem'  => [
                's3' => [['disk_name' => 's3']]
            ]
        ]);
    }

    public function testCheckS3Fail()
    {
        $response = $this->call('GET', 'api/health');
        $response->assertStatus(503)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::S3 . '/s3',
                    'available' => false,
                    'message' => trans('healthcheck::messages.ErrorConnectionS3'),
                    'metadata' => []
                ]]
            ]);
    }

    public function testCheckS3Success()
    {
        $healthStorage = new HealthTest();
        $storage = $healthStorage->mockStorageDisk('s3');
        $storage->shouldReceive('exists')->once()->andReturn(true);

        $response = $this->call('GET', 'api/health');
        $response->assertStatus(200)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::S3 . '/s3',
                    'available' => true,
                    'message' => null,
                    'metadata' => []
                ]]
            ]);
    }
}
