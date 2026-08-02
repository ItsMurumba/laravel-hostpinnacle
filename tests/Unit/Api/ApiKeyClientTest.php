<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\ApiKeyClient;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('apiKeys() returns an ApiKeyClient', function () {
    expect((new Hostpinnacle())->apiKeys())->toBeInstanceOf(ApiKeyClient::class);
});

test('create() sends post request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/apikey/create' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->apiKeys()->create();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/apikey/create'
            && $request->method() === 'POST'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('read() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/apikey/read*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->apiKeys()->read();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/apikey/read');
    });
});

test('update() sends post request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/apikey/update' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->apiKeys()->update();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/apikey/update'
            && $request->method() === 'POST';
    });
});

test('delete() sends post request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/apikey/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->apiKeys()->delete();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/apikey/delete'
            && $request->method() === 'POST';
    });
});
