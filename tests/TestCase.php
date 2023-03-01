<?php

namespace Itsmurumba\Hostpinnacle\Tests;

use Itsmurumba\Hostpinnacle\HostpinnacleServiceProvider;
use Mockery;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{

    protected $hostpinnacle;
    protected $mock;

    public function setUp(): void
    {
        parent::setUp();
        $this->hostpinnacle = Mockery::mock('Itsmurumba\Hostpinnacle\Hostpinnacle');
        $this->mock = Mockery::mock('GuzzleHttp\Client');
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
