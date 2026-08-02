<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\MessageTemplateClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('messageTemplate() returns a MessageTemplateClient', function () {
    expect((new Hostpinnacle())->messageTemplate())->toBeInstanceOf(MessageTemplateClient::class);
});

test('create() sends post request with message', function () {
    Http::fake([
        'https://api.hostpinnacle.test/template/create' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->messageTemplate()->create([
        'message' => 'This is test message ###123### from Test',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/template/create'
            && $request->method() === 'POST'
            && $request['message'] === 'This is test message ###123### from Test'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('create() throws IsNullException when message is missing', function () {
    (new Hostpinnacle())->messageTemplate()->create([]);
})->throws(IsNullException::class, 'message must not be null');

test('read() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/template/read*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->messageTemplate()->read();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/template/read');
    });
});

test('update() sends post request with message and id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/template/update' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->messageTemplate()->update([
        'message' => 'This is test message to update or modify',
        'id' => '2',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/template/update'
            && $request->method() === 'POST'
            && $request['message'] === 'This is test message to update or modify'
            && $request['id'] === '2';
    });
});

test('update() throws IsNullException when message or id missing', function () {
    (new Hostpinnacle())->messageTemplate()->update(['message' => 'Only message']);
})->throws(IsNullException::class, 'message and id must not be null');

test('delete() sends post request with id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/template/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->messageTemplate()->delete(['id' => '2,1']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/template/delete'
            && $request->method() === 'POST'
            && $request['id'] === '2,1';
    });
});

test('delete() throws IsNullException when id missing', function () {
    (new Hostpinnacle())->messageTemplate()->delete([]);
})->throws(IsNullException::class, 'id must not be null');
