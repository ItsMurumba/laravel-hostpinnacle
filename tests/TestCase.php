<?php

namespace Itsmurumba\Hostpinnacle\Tests;

use Itsmurumba\Hostpinnacle\HostpinnacleServiceProvider;
use Mockery;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            HostpinnacleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
