<?php

namespace Zanichelli\HealthCheck\Tests\Feature;

use Zanichelli\HealthCheck\Tests\TestCase;
use Zanichelli\HealthCheck\Http\Constants\Service;

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
        $app['config']->set(
            'healthcheck',
            [
                'filesystem' => [
                    'local' => [
                        'disk_name' => 'local',
                        'volume_path' => './',
                        'free_size_limit' => 1
                    ]
                ]
            ]
        );
    }

    public function testCheckFilesystemSuccess()
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

    public function testCheckFilesystemNoSpace()
    {
        $this->app['config']->set(
            'healthcheck.filesystem.local',
            [
                'disk_name' => 'local',
                'volume_path' => './',
                'free_size_limit' => 999999999999999
            ]
        );

        $response = $this->call('GET', 'api/health');
        $content = json_decode($response->getContent());
        $response->assertStatus(200)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::FILESYSTEM . '/local',
                    'available' => true,
                    'message' => null,
                    'metadata' => [
                        'freespace' => [
                            'size' => $content->status[0]->metadata->freespace->size,
                            'unit' => 'Megabyte',
                            'message' => trans('healthcheck::messages.filesystem.NoDiskSpace')
                        ]
                    ]
                ]]
            ]);
    }

    public function testCheckFilesystemError()
    {
        $this->app['config']->set(
            'healthcheck.filesystem.local',
            [
                'disk_name' => 'test',
                'volume_path' => './'
            ]
        );

        $response = $this->call('GET', 'api/health');
        $response->assertStatus(503)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::FILESYSTEM . '/test',
                    'available' => false,
                    'message' => trans('healthcheck::messages.DiskNotAvailable'),
                    'metadata' => []
                ]]
            ]);
    }

    public function testCheckFilesystemPathError()
    {
        $this->app['config']->set(
            'healthcheck.filesystem.local',
            [
                'disk_name' => 'local',
                'volume_path' => './ErrorPath'
            ]
        );

        $response = $this->call('GET', 'api/health');
        $response->assertStatus(503)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::FILESYSTEM . '/local',
                    'available' => false,
                    'message' => trans('healthcheck::messages.DiskNotAvailable'),
                    'metadata' => []
                ]]
            ]);
    }
}
