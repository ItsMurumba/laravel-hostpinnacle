<?php

use Illuminate\Http\Client\Response;
use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\HostpinnacleCredentials;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('Hostpinnacle uses credentials from HostpinnacleCredentials when provided', function () {
    Http::fake([
        'https://custom.api.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $credentials = new HostpinnacleCredentials(
        'custom-api-key',
        'CUSTOMID',
        'customuser',
        'custompass',
        'https://custom.api.test'
    );

    $hostpinnacle = new Hostpinnacle($credentials);
    /** @var Response $response */
    $response = $hostpinnacle->sendQuickSMS([
        'msg' => 'Test',
        'mobile' => '254700000000',
    ]);

    expect($response)->not->toBeNull();
    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://custom.api.test/send')
            && $request->hasHeader('apikey', 'custom-api-key');
    });
});

test('Hostpinnacle uses config when constructed with null credentials', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    /** @var Response $response */
    $response = $hostpinnacle->sendQuickSMS([
        'msg' => 'Test',
        'mobile' => '254700000000',
    ]);

    expect($response)->not->toBeNull();
    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->hasHeader('apikey', 'test-api-key');
    });
});
