<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('sends post request with correct payload', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    $response = $hostpinnacle->sendQuickSMS([
        'msg' => 'Hello World',
        'mobile' => '254700000000',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/send'
            && $request['msg'] === 'Hello World'
            && $request['mobile'] === '254700000000'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('sends trackLink and smartLinkTitle when provided', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    $hostpinnacle->sendQuickSMS([
        'msg' => 'Check out https://example.com',
        'mobile' => '254700000000',
        'trackLink' => 'true',
        'smartLinkTitle' => 'My Example Link',
    ]);

    Http::assertSent(function ($request) {
        return $request['trackLink'] === 'true'
            && $request['smartLinkTitle'] === 'My Example Link';
    });
});

test('omits trackLink and smartLinkTitle when not provided', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    $hostpinnacle->sendQuickSMS([
        'msg' => 'Hello World',
        'mobile' => '254700000000',
    ]);

    Http::assertSent(function ($request) {
        return !isset($request['trackLink']) && !isset($request['smartLinkTitle']);
    });
});

test('throws IsNullException when msg is missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendQuickSMS(['mobile' => '254700000000']);
})->throws(IsNullException::class, 'msg and mobile must not be null');

test('throws IsNullException when mobile is missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendQuickSMS(['msg' => 'Hello']);
})->throws(IsNullException::class, 'msg and mobile must not be null');

test('throws IsNullException when both msg and mobile are missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendQuickSMS([]);
})->throws(IsNullException::class, 'msg and mobile must not be null');
