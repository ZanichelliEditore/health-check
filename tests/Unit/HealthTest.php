<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper;
use Zanichelli\HealthCheck\Http\Controllers\HealthController;

class HealthTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function checkDbPositiveTest()
    {
        $mock = Mockery::mock(HealthCheckWrapper::class)->makePartial()
            ->shouldReceive([
                'getDbConnection' => true,
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();
        $this->app->instance('Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper', $mock);
        $healthController = new HealthController($mock);
        $this->assertEquals($healthController->checkDb(), null);
        $this->assertEquals($healthController->getHealth(), array('db' => array('status' => 'ok')));
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkDbNegativeTest()
    {
        $mock = Mockery::mock(HealthCheckWrapper::class)->makePartial()
            ->shouldReceive([
                'getDbConnection' => false,
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();
        $this->app->instance('Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper', $mock);
        $healthController = new HealthController($mock);
        $this->assertEquals($healthController->checkDb(), null);
        $this->assertEquals(
            $healthController->getHealth(),
            array(
                'db' => array(
                    'status' => 'ko',
                    'message' => trans('healthcheck::messages.DatabaseConnectionNotAvailable'),
                )
            )
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkLocalStoragePositiveTest()
    {
        $storageMock = Mockery::mock(Storage::disk('local'))->makePartial()
            ->shouldReceive([
                'put' => true,
                'delete' => true,
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $mock = Mockery::mock(HealthCheckWrapper::class)->makePartial()
            ->shouldReceive([
                'storageDisk' => $storageMock,
            ])
            ->withAnyArgs()
            ->twice()
            ->getMock();

        $this->app->instance('Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper', $mock);
        $healthController = new HealthController($mock);
        $this->assertEquals($healthController->checkLocalStorage(), null);
        $this->assertEquals($healthController->getHealth(), array('localstorage' => array('status' => 'ok')));
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkLocalStorageNegativeTest()
    {
        $storageMock = Mockery::mock(Storage::disk('local'))->makePartial()
            ->shouldReceive([
                'put' => false,
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $mock = Mockery::mock(HealthCheckWrapper::class)->makePartial()
            ->shouldReceive([
                'storageDisk' => $storageMock,
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $this->app->instance('Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper', $mock);
        $healthController = new HealthController($mock);
        $this->assertEquals($healthController->checkLocalStorage(), null);
        $this->assertEquals($healthController->getHealth(), array('localstorage' => array(
            'status' => 'ko',
            'message' => trans('healthcheck::messages.UnableToWriteOnDisk', ['storage' => 'local'])
        )));
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkS3StoragePositiveTest()
    {
        $storageMock = Mockery::mock(Storage::disk('s3'))->makePartial()
            ->shouldReceive([
                'files' => array(),
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $mock = Mockery::mock(HealthCheckWrapper::class)->makePartial()
            ->shouldReceive([
                'storageDisk' => $storageMock,
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $this->app->instance('Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper', $mock);
        $healthController = new HealthController($mock);
        $this->assertEquals($healthController->checkS3Storage(), null);
        $this->assertEquals($healthController->getHealth(), array('s3storage' => array('status' => 'ok')));
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkS3StorageNegativeTest()
    {
        $storageMock = Mockery::mock(Storage::disk('s3'))->makePartial()
            ->shouldReceive('files')
            ->andThrow(\Exception::class, 'failed somewhere')
            ->withAnyArgs()
            ->once()
            ->getMock();

        $mock = Mockery::mock(HealthCheckWrapper::class)->makePartial()
            ->shouldReceive([
                'storageDisk' => $storageMock,
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();

        $this->app->instance('Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper', $mock);
        $healthController = new HealthController($mock);
        $this->assertEquals($healthController->checkS3Storage(), null);
        $this->assertEquals($healthController->getHealth(), array('s3storage' => array(
            'status' => 'ko',
            'message' => 'failed somewhere'
        )));
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkFreeSpacePositiveTest()
    {
        $mock = Mockery::mock(HealthCheckWrapper::class)->makePartial()
            ->shouldReceive([
                'disk_free_space' => (config('healthcheck.free_size_limit') + 1),
            ])
            ->withAnyArgs()
            ->once()
            ->getMock();
        $this->app->instance('Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper', $mock);
        $healthController = new HealthController($mock);
        $this->assertEquals($healthController->checkFreeSpace(), null);
        $this->assertEquals($healthController->getHealth(), array('freeSpace' => array('status' => 'ok')));
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkFreeSpaceNegativeTest()
    {
        $mock = Mockery::mock(HealthCheckWrapper::class)->makePartial()
            ->shouldReceive([
                'disk_free_space' => (config('healthcheck.free_size_limit') - 1),
            ])
            ->withAnyArgs()
            ->twice()
            ->getMock();
        $this->app->instance('Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper', $mock);
        $healthController = new HealthController($mock);
        $this->assertEquals($healthController->checkFreeSpace(), null);
        $this->assertEquals(
            $healthController->getHealth(),
            array('freeSpace' => array(
                'status' => 'ko',
                'message' => trans('healthcheck::messages.NoDiskSpace', ['space' => (config('healthcheck.free_size_limit') - 1)])
            ))
        );
    }
}
