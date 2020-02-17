<?php

namespace Zanichelli\HealthCheck\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Zanichelli\HealthCheck\Tests\TestCase;
use Zanichelli\HealthCheck\Http\Constants\Service;

class FilesystemHealthTest extends TestCase
{

    protected function setup(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

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
                        'volume_path' => './',
                        'free_size_limit' => 1
                    ]
                ]
            ]
        );
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

    /**
     * @test
     *
     * @return void
     */
    public function checkFilesystemNoSpace()
    {
        $this->app['config']->set(
            'healthcheck.filesystem.local',
            [
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

    /**
     * @test
     *
     * @return void
     */
    public function checkFilesystemError()
    {
        $this->app['config']->set(
            'healthcheck.filesystem.local',
            [
                'disk_name' => 'test',
                'volume_path' => './'
            ]
        );

        $response = $this->call('GET', 'api/health');
        $content = json_decode($response->getContent());
        $response->assertStatus(400)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::FILESYSTEM . '/test',
                    'available' => false,
                    'message' => trans('healthcheck::messages.DiskNotAvailable'),
                    'metadata' => []
                ]]
            ]);
    }
}
