<?php

namespace Zanichelli\HealthCheck\Providers;

use Illuminate\Support\ServiceProvider;

class HealthCheckServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/healthcheck.php',
            'healthcheck'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load package files
        $this->app['router']->namespace('Zanichelli\HealthCheck\Http\Controllers')
            ->middleware(['api'])
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            });
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'healthcheck');

        // Publishing files
        $this->publishes([
            __DIR__ . '/../config/healthcheck.php' => config_path('healthcheck.php')
        ], 'config');
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/healthcheck'),
        ], 'resources');
    }
}
