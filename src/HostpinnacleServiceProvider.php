<?php

namespace Itsmurumba\Hostpinnacle;

use Illuminate\Support\ServiceProvider;
use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Console\InstallHostpinnaclePackage;
use Itsmurumba\Hostpinnacle\HostpinnacleFactory;

/**
 * Service provider for the Hostpinnacle package. Registers config, migrations, routes, and bindings.
 */
class HostpinnacleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the package: publish config/migrations, load SaaS routes when enabled.
     *
     * @return void
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
     * Register the package services: merge default config, hostpinnacle singleton and HostpinnacleFactory.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/hostpinnacle.php', 'hostpinnacle');

        $this->app->singleton('hostpinnacle', function () {
            return new Hostpinnacle();
        });

        $this->app->alias('hostpinnacle', 'laravel-hostpinnacle');

        $this->app->singleton(HostpinnacleFactory::class, function () {
            return new HostpinnacleFactory();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides()
    {
        return ['hostpinnacle', 'laravel-hostpinnacle'];
    }
}
