<?php

namespace Itsmurumba\Hostpinnacle\Tests\Support;

use Itsmurumba\Hostpinnacle\Tests\TestCase;

/**
 * TestCase with SaaS mode enabled at boot time, so the package's conditional
 * migrations/routes (registered in HostpinnacleServiceProvider::boot) load.
 *
 * Drops auth:sanctum/auth from the route middleware since Sanctum isn't a
 * project dependency; HostpinnacleAccountController enforces its own 401/403
 * checks via Auth::user(), so route-level auth middleware isn't required for
 * that to be testable.
 */
class SaasEnabledTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('hostpinnacle.saas.enabled', true);
        $app['config']->set('hostpinnacle.saas.owner_model', 'Workbench\\App\\Models\\User');
        $app['config']->set('hostpinnacle.saas.api_middleware', ['api']);
        $app['config']->set('hostpinnacle.saas.web_middleware', ['web']);
    }
}
