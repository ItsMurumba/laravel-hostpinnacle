<?php

use Illuminate\Support\Facades\Http;
use Itsmurumba\Hostpinnacle\Facades\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Hostpinnacle as HostpinnacleClient;
use Itsmurumba\Hostpinnacle\HostpinnacleCredentials;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('facade proxies sendQuickSMS to the bound singleton', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $response = Hostpinnacle::sendQuickSMS([
        'msg' => 'Hello World',
        'mobile' => '254700000000',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->hasHeader('apikey', 'test-api-key')
            && $request['msg'] === 'Hello World';
    });
});

test('facade for() builds a Hostpinnacle client from the given credentials', function () {
    Http::fake([
        'https://facade-api.test/send*' => Http::response(['status' => 'ok'], 200),
    ]);

    $credentials = new HostpinnacleCredentials(
        'facade-key',
        'FID',
        'facadeuser',
        'facadepass',
        'https://facade-api.test'
    );

    $client = Hostpinnacle::for($credentials);

    expect($client)->toBeInstanceOf(HostpinnacleClient::class);

    $client->sendQuickSMS(['msg' => 'Hi', 'mobile' => '254700000000']);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://facade-api.test')
            && $request->hasHeader('apikey', 'facade-key');
    });
});
