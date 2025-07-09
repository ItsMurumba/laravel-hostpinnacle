<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

test('the install command copies the configuration', function () {
    if (File::exists(config_path('hostpinnacle.php'))) {
        unlink(config_path('hostpinnacle.php'));
    }

    expect(File::exists(config_path('hostpinnacle.php')))->toBeFalse();

    Artisan::call('hostpinnacle:install');

    expect(File::exists(config_path('hostpinnacle.php')))->toBeTrue();
});

test('when a config file is present users can choose not to overwrite it', function () {
    File::put(config_path('hostpinnacle.php'), 'test contents');
    expect(File::exists(config_path('hostpinnacle.php')))->toBeTrue();

    $command = $this->artisan('hostpinnacle:install');

    $command->expectsConfirmation(
        'Config file already exists. Do you want to overwrite it?',
        'no'
    );

    $command->expectsOutput('Exiting. Hostpinnacle configuration was not overwritten');
    expect(file_get_contents(config_path('hostpinnacle.php')))->toBe('test contents');

    unlink(config_path('hostpinnacle.php'));
});
