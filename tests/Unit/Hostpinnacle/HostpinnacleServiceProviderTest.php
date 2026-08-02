<?php

use Illuminate\Support\Facades\Route;
use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\HostpinnacleFactory;

test('merges the default hostpinnacle config', function () {
    expect(config('hostpinnacle.saas.table'))->toBe('hostpinnacle_accounts')
        ->and(config('hostpinnacle.saas.owner_key'))->toBe('user_id')
        ->and(config('hostpinnacle.saas.enabled'))->toBeFalse();
});

test('binds the hostpinnacle singleton and its alias', function () {
    $hostpinnacle = app('hostpinnacle');

    expect($hostpinnacle)->toBeInstanceOf(Hostpinnacle::class)
        ->and(app('hostpinnacle'))->toBe($hostpinnacle)
        ->and(app('laravel-hostpinnacle'))->toBe($hostpinnacle);
});

test('binds the HostpinnacleFactory singleton', function () {
    $factory = app(HostpinnacleFactory::class);

    expect($factory)->toBeInstanceOf(HostpinnacleFactory::class)
        ->and(app(HostpinnacleFactory::class))->toBe($factory);
});

test('does not register saas routes when saas is disabled', function () {
    expect(Route::has('hostpinnacle.api.accounts.index'))->toBeFalse()
        ->and(Route::has('hostpinnacle.web.accounts.index'))->toBeFalse();
});
