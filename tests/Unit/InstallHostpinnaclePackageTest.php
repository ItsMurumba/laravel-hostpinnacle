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

    /**
     * @test
     *
     * @return void
     */
    public function when_a_config_file_is_present_users_can_choose_not_to_overwrite_it()
    {
        File::put(config_path('hostpinnacle.php'), 'test contents');
        $this->assertTrue(File::exists(config_path('hostpinnacle.php')));

        $command = $this->artisan('hostpinnacle:install');

        $command->expectsConfirmation(
            'Config file already exists. Do you want to overwrite it?',
            'no'
        );

        $command->expectsOutput('Exiting. Hostpinnacle configuration was not overwritten');
        $this->assertEquals('test contents', file_get_contents(config_path('hostpinnacle.php')));

        unlink(config_path('hostpinnacle.php'));
    }
}
