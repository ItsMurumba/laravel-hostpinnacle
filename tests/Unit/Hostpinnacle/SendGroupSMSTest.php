<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('sends post request with group ids', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send*' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    $response = $hostpinnacle->sendGroupSMS([
        'msg' => 'Group message',
        'groupIds' => '1,2,3',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/send')
            && $request->hasHeader('apikey', 'test-api-key')
            && $request['group'] === '1,2,3';
    });
});

test('sends trackLink and smartLinkTitle when provided', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send*' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    $hostpinnacle->sendGroupSMS([
        'msg' => 'Check out https://example.com',
        'groupIds' => '1,2,3',
        'trackLink' => 'true',
        'smartLinkTitle' => 'My Example Link',
    ]);

    Http::assertSent(function ($request) {
        return $request['trackLink'] === 'true'
            && $request['smartLinkTitle'] === 'My Example Link';
    });
});

test('throws IsNullException when msg is missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendGroupSMS(['groupIds' => '1']);
})->throws(IsNullException::class, 'msg and groupIds must not be null');

test('throws IsNullException when groupIds is missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendGroupSMS(['msg' => 'Hello']);
})->throws(IsNullException::class, 'msg and groupIds must not be null');
