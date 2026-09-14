<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\DraftClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('draft() returns a DraftClient', function () {
    expect((new Hostpinnacle())->draft())->toBeInstanceOf(DraftClient::class);
});

test('create() sends post request with title and content', function () {
    Http::fake([
        'https://api.hostpinnacle.test/draft/create' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->draft()->create([
        'title' => 'This is a new draft title.',
        'content' => 'This is a new draft content.',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/draft/create'
            && $request->method() === 'POST'
            && $request['title'] === 'This is a new draft title.'
            && $request['content'] === 'This is a new draft content.'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('create() throws IsNullException when title or content missing', function () {
    (new Hostpinnacle())->draft()->create(['title' => 'Only title']);
})->throws(IsNullException::class, 'title and content must not be null');

test('read() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/draft/read*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->draft()->read();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/draft/read');
    });
});

test('update() sends post request with title, content and id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/draft/update' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->draft()->update([
        'title' => 'Title to modify',
        'content' => 'Modify my existing draft',
        'id' => '15',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/draft/update'
            && $request->method() === 'POST'
            && $request['title'] === 'Title to modify'
            && $request['content'] === 'Modify my existing draft'
            && $request['id'] === '15';
    });
});

test('update() throws IsNullException when title, content, or id missing', function () {
    (new Hostpinnacle())->draft()->update(['title' => 'Title', 'content' => 'Content']);
})->throws(IsNullException::class, 'title, content and id must not be null');

test('delete() sends post request with id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/draft/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->draft()->delete(['id' => '15,121']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/draft/delete'
            && $request->method() === 'POST'
            && $request['id'] === '15,121';
    });
});

test('delete() throws IsNullException when id missing', function () {
    (new Hostpinnacle())->draft()->delete([]);
})->throws(IsNullException::class, 'id must not be null');
