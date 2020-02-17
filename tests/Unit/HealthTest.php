<?php

namespace Zanichelli\HealthCheck\Tests\Unit;

use Mockery as m;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Zanichelli\HealthCheck\Http\Constants\Service;
use Zanichelli\HealthCheck\Tests\TestCase;
use Zanichelli\HealthCheck\Http\Services\HealthCheckService;
use Zanichelli\HealthCheck\Http\Controllers\HealthController;

class HealthTest extends TestCase
{

    /**
     * Create a mock of a Storage disk. 

     * @param  String $disk Optional
     * @return Filesystem
     */
    protected function mockStorageDisk($disk = 'mock')
    {
        Storage::extend('mock', function () {
            return m::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        });

        Config::set('filesystems.disks.' . $disk, ['driver' => 'mock']);
        Config::set('filesystems.default', $disk);

        return Storage::disk($disk);
    }

    /**
     * @return array
     */
    private function getArrayStatus()
    {
        return [
            [
                'service' => Service::DATABASE . '/mysql',
                'available' => true,
                'message' => null,
                'metadata' => []
            ],
            [
                'service' => Service::S3 . '/s3',
                'available' => true,
                'message' => null,
                'metadata' => []
            ],
        ];
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkEmptySystem()
    {
        $mock = m::mock(HealthCheckService::class)->makePartial()
            ->shouldReceive([
                'checkSystem' => [],
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $controller = new HealthController($mock);

        $response = $controller->index();

        $this->assertEquals($response->getStatusCode(), 204);
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkSuccesSystems()
    {
        $mock = m::mock(HealthCheckService::class)->makePartial()
            ->shouldReceive([
                'checkSystem' => $this->getArrayStatus()
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $controller = new HealthController($mock);
        $response = $controller->index();

        $this->assertEquals($response->getStatusCode(), 200);
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkFailSystems()
    {
        $data = $this->getArrayStatus();
        $data[0]['available'] = false;
        $mock = m::mock(HealthCheckService::class)->makePartial()
            ->shouldReceive([
                'checkSystem' => $data
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $controller = new HealthController($mock);
        $response = $controller->index();

        $this->assertEquals($response->getStatusCode(), 400);
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkFailSaveFilesystem()
    {
        $this->app['config']->set(
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
        $storage = $this->mockStorageDisk('local');
        $storage->shouldReceive('put')->once()->andReturn(false);
        $response = $this->call('GET', 'api/health');
        $response->assertStatus(400)
            ->assertExactJson([
                'status' => [[
                    'service' => Service::FILESYSTEM . '/local',
                    'available' => false,
                    'message' => trans('healthcheck::messages.filesystem.WritingError'),
                    'metadata' => []
                ]]
            ]);
    }
}
