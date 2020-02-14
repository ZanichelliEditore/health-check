<?php

namespace Zanichelli\HealthCheck\Tests\Feature;

use Zanichelli\HealthCheck\Http\Constants\Service;
use Zanichelli\HealthCheck\Tests\TestCase;

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
        $app['config']->set('healthcheck.filesystem.s3', [['disk_name' => 's3']]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkS3Fail()
    {
        $response = $this->call('GET', 'api/health');
        $response->assertStatus(400)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::S3 . '/s3',
                    'available' => false,
                    'message' => trans('healthcheck::messages.ErrorConnectionS3'),
                    'metadata' => []
                ]]
            ]);
    }
}
