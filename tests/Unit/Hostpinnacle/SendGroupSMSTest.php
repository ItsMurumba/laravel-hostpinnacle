<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('sends get request with group ids', function () {
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
        return str_contains($request->url(), 'https://api.hostpinnacle.test/send')
            && $request->hasHeader('apikey', 'test-api-key');
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
