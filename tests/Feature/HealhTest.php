<?php

namespace Zanichelli\HealthCheck\Tests\Feature;

use Zanichelli\HealthCheck\Tests\TestCase;

class HealthTest extends TestCase
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
            'db' => [],
            'filesystem' => [
                'local' => []
            ]
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function checkEmptyConfigurations()
    {
        $response = $this->call('GET', 'api/health');
        $response->assertStatus(204);
    }
}
