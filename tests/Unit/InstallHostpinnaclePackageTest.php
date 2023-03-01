<?php

namespace Itsmurumba\Hostpinnacle\Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Itsmurumba\Hostpinnacle\Tests\TestCase;

class InstallHostpinnaclePackageTest extends TestCase
{

    /**
     * @test
     *
     * @return void
     */
    public function the_install_command_copies_the_configuration()
    {
        if (File::exists(config_path('hostpinnacle.php'))) {
            unlink(config_path('hostpinnacle.php'));
        }

        $this->assertFalse(File::exists(config_path('hostpinnacle.php')));

        Artisan::call('hostpinnacle:install');

        $this->assertTrue(File::exists(config_path('hostpinnacle.php')));
    }
}
