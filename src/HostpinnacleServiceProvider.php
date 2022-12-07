<?php

namespace Itsmurumba\Hostpinnacle;

use Illuminate\Support\ServiceProvider;
use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Console\InstallHostpinnaclePackage;

class HostpinnacleServiceProvider extends ServiceProvider
{
    /**
     * Publishes all the config file this package needs to function
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/../resources/config/hostpinnacle.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $config => config_path('hostpinnacle.php')
            ], 'hostpinnacle-config');

            $this->commands([
                InstallHostpinnaclePackage::class,
            ]);
        }
    }

    /**
     * Register the application services
     */
    public function register()
    {
        $this->app->bind('laravel-hostpinnacle', function () {

            return new Hostpinnacle;
        });
    }

    /**
     * Get the services provided by the provider
     * @return array
     */
    public function provides()
    {

        return ['laravel-hostpinnacle'];
    }
}
