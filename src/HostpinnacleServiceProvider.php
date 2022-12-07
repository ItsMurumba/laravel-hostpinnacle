<?php

namespace Itsmurumba\Hostpinnacle;

use Illuminate\Support\ServiceProvider;
use Itsmurumba\Hostpinnacle\Hostpinnacle;

class HostpinnacleServiceProvider extends ServiceProvider
{
    /**
     * Publishes all the config file this package needs to function
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/../resources/config/hostpinnacle.php');

        $this->publishes([
            $config => config_path('hostpinnacle.php')
        ]);
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
