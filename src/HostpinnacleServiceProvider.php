<?php

namespace Itsmurumba\Hostpinnacle;

use Illuminate\Support\ServiceProvider;
use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Console\InstallHostpinnaclePackage;
use Itsmurumba\Hostpinnacle\HostpinnacleFactory;

class HostpinnacleServiceProvider extends ServiceProvider
{
    /**
     * Publishes all the config file this package needs to function
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/../config/hostpinnacle.php');

        if (config('hostpinnacle.saas.enabled', false)) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            if (config('hostpinnacle.saas.api_routes_enabled', true)) {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            }
            if (config('hostpinnacle.saas.web_routes_enabled', true)) {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            }
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $config => config_path('hostpinnacle.php')
            ], 'hostpinnacle-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'hostpinnacle-migrations');

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
        $this->app->singleton('hostpinnacle', function () {
            return new Hostpinnacle();
        });

        $this->app->alias('hostpinnacle', 'laravel-hostpinnacle');

        $this->app->singleton(HostpinnacleFactory::class, function () {
            return new HostpinnacleFactory();
        });
    }

    /**
     * Get the services provided by the provider
     * @return array
     */
    public function provides()
    {
        return ['hostpinnacle', 'laravel-hostpinnacle'];
    }
}
