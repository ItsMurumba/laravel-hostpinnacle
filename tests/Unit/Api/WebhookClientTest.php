<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\WebhookClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('webhook() returns a WebhookClient', function () {
    expect((new Hostpinnacle())->webhook())->toBeInstanceOf(WebhookClient::class);
});

test('create() sends post request with smswebhook and smswebhookrate', function () {
    Http::fake([
        'https://api.hostpinnacle.test/webhook/create' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->webhook()->create([
        'smswebhook' => 'https://google.com',
        'smswebhookrate' => '10',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/webhook/create'
            && $request->method() === 'POST'
            && $request['smswebhook'] === 'https://google.com'
            && $request['smswebhookrate'] === '10'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('create() omits smswebhookrate when not provided', function () {
    Http::fake([
        'https://api.hostpinnacle.test/webhook/create' => Http::response(['status' => 'success'], 200),
    ]);

    (new Hostpinnacle())->webhook()->create(['smswebhook' => 'https://google.com']);

    Http::assertSent(function ($request) {
        return !isset($request['smswebhookrate']);
    });
});

test('create() throws IsNullException when smswebhook is missing', function () {
    (new Hostpinnacle())->webhook()->create([]);
})->throws(IsNullException::class, 'smswebhook must not be null');

test('read() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/webhook/read*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->webhook()->read();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/webhook/read');
    });
});

test('update() sends post request with smswebhook', function () {
    Http::fake([
        'https://api.hostpinnacle.test/webhook/update' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->webhook()->update(['smswebhook' => 'https://test.com']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/webhook/update'
            && $request->method() === 'POST'
            && $request['smswebhook'] === 'https://test.com';
    });
});

test('update() throws IsNullException when smswebhook is missing', function () {
    (new Hostpinnacle())->webhook()->update([]);
})->throws(IsNullException::class, 'smswebhook must not be null');

test('delete() sends post request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/webhook/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->webhook()->delete();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/webhook/delete'
            && $request->method() === 'POST';
    });
});
