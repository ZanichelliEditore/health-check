<?php

namespace Zanichelli\HealthCheck\Tests\Feature;

use Zanichelli\HealthCheck\Http\Constants\Service;
use Zanichelli\HealthCheck\Tests\TestCase;

class FilesystemHealthTest extends TestCase
{

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('healthcheck.filesystem.local', ['volume_path' => './']);
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkFilesystemSuccess()
    {
        $response = $this->call('GET', 'api/health');
        $response->assertStatus(200)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::FILESYSTEM . '/local',
                    'available' => true,
                    'message' => null,
                    'metadata' => []
                ]]
            ]);
    }
}
