<?php

namespace Zanichelli\HealthCheck\Tests\Unit;

use Mockery as m;
use Zanichelli\HealthCheck\Http\Constants\Service;
use Zanichelli\HealthCheck\Tests\TestCase;
use Zanichelli\HealthCheck\Http\Services\HealthCheckService;
use Zanichelli\HealthCheck\Http\Controllers\HealthController;

class HealthTest extends TestCase
{

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
}
