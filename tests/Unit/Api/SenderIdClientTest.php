<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\SenderIdClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('senderId() returns a SenderIdClient', function () {
    expect((new Hostpinnacle())->senderId())->toBeInstanceOf(SenderIdClient::class);
});

test('create() sends post request with senderid', function () {
    Http::fake([
        'https://api.hostpinnacle.test/senderid/create' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->senderId()->create(['senderid' => 'MYBRAND']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/senderid/create'
            && $request->method() === 'POST'
            && $request['userid'] === 'testuser'
            && $request['senderid'] === 'MYBRAND'
            && $request['output'] === 'json'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('create() throws IsNullException when senderid is missing', function () {
    (new Hostpinnacle())->senderId()->create([]);
})->throws(IsNullException::class, 'senderid must not be null');

test('read() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/senderid/read*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->senderId()->read();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/senderid/read')
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('update() sends post request with senderid and id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/senderid/update' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->senderId()->update(['senderid' => 'NEWBRAND', 'id' => '69']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/senderid/update'
            && $request->method() === 'POST'
            && $request['senderid'] === 'NEWBRAND'
            && $request['id'] === '69';
    });
});

test('update() throws IsNullException when senderid or id missing', function () {
    (new Hostpinnacle())->senderId()->update(['senderid' => 'NEWBRAND']);
})->throws(IsNullException::class, 'senderid and id must not be null');

test('delete() sends post request with id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/senderid/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->senderId()->delete(['id' => '61,68']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/senderid/delete'
            && $request->method() === 'POST'
            && $request['id'] === '61,68';
    });
});

test('delete() sends post request with senderid', function () {
    Http::fake([
        'https://api.hostpinnacle.test/senderid/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->senderId()->delete(['senderid' => 'ABCDEF1,ABCDEF2']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request['senderid'] === 'ABCDEF1,ABCDEF2' && !isset($request['id']);
    });
});

test('delete() throws IsNullException when both id and senderid are missing', function () {
    (new Hostpinnacle())->senderId()->delete([]);
})->throws(IsNullException::class, 'id or senderid must not be null');
