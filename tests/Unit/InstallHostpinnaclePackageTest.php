<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

test('the install command copies the configuration', function () {
    $configPath = $this->app->configPath('hostpinnacle.php');

    if (File::exists($configPath)) {
        unlink($configPath);
    }

    expect(File::exists($configPath))->toBeFalse();

    Artisan::call('hostpinnacle:install');

    expect(File::exists($configPath))->toBeTrue();
});

test('when a config file is present users can choose not to overwrite it', function () {
    $configPath = $this->app->configPath('hostpinnacle.php');

    File::put($configPath, 'test contents');
    expect(File::exists($configPath))->toBeTrue();

    $this->artisan('hostpinnacle:install')
        ->expectsConfirmation(
            'Config file already exists. Do you want to overwrite it?',
            'no'
        )
        ->expectsOutput('Exiting. Hostpinnacle configuration was not overwritten')
        ->run();

    expect(file_get_contents($configPath))->toBe('test contents');

    unlink($configPath);
});
