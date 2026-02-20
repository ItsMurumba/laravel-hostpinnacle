<?php

use Illuminate\Http\Client\Response;
use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\HostpinnacleCredentials;
use Itsmurumba\Hostpinnacle\HostpinnacleFactory;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
    config()->set('hostpinnacle.saas.table', 'hostpinnacle_accounts');
});

test('HostpinnacleFactory for HostpinnacleCredentials returns Hostpinnacle using those credentials', function () {
    Http::fake([
        'https://factory-api.test/send*' => Http::response(['status' => 'ok'], 200),
    ]);

    $credentials = new HostpinnacleCredentials(
        'factory-key',
        'FID',
        'factoryuser',
        'factorypass',
        'https://factory-api.test'
    );

    $factory = app(HostpinnacleFactory::class);
    $hostpinnacle = $factory->for($credentials);

    expect($hostpinnacle)->toBeInstanceOf(Hostpinnacle::class);

    /** @var Response $response */
    $response = $hostpinnacle->sendQuickSMS(['msg' => 'Hi', 'mobile' => '254700000000']);
    expect($response)->not->toBeNull();
    expect($response->successful())->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://factory-api.test')
            && $request->hasHeader('apikey', 'factory-key');
    });
});

test('HostpinnacleFactory for HostpinnacleAccount returns Hostpinnacle using account credentials', function () {
    Http::fake([
        'https://account-api.test/send*' => Http::response(['status' => 'ok'], 200),
    ]);

    $account = new HostpinnacleAccount([
        'api_key' => 'account-key',
        'sender_id' => 'AID',
        'username' => 'accountuser',
        'password' => 'accountpass',
        'base_url' => 'https://account-api.test',
    ]);

    $factory = app(HostpinnacleFactory::class);
    $hostpinnacle = $factory->for($account);

    expect($hostpinnacle)->toBeInstanceOf(Hostpinnacle::class);

    /** @var Response $response */
    $response = $hostpinnacle->sendQuickSMS(['msg' => 'Hi', 'mobile' => '254700000000']);
    expect($response)->not->toBeNull();
    expect($response->successful())->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://account-api.test')
            && $request->hasHeader('apikey', 'account-key');
    });
});

// Hostpinnacle::for($account) is a thin wrapper around app(HostpinnacleFactory::class)->for($account);
// and is covered by the factory tests above.
