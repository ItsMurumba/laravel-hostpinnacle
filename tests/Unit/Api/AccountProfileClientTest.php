<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\AccountProfileClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('accountProfile() returns an AccountProfileClient', function () {
    expect((new Hostpinnacle())->accountProfile())->toBeInstanceOf(AccountProfileClient::class);
});

test('readStatus() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/account/readstatus*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->accountProfile()->readStatus();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/account/readstatus');
    });
});

test('readProfile() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/account/readprofile*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->accountProfile()->readProfile();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/account/readprofile');
    });
});

test('updateProfile() sends post request with only the given fields', function () {
    Http::fake([
        'https://api.hostpinnacle.test/account/updateprofile' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->accountProfile()->updateProfile([
        'fullname' => 'Testing',
        'city' => 'Mumbai',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/account/updateprofile'
            && $request->method() === 'POST'
            && $request['fullname'] === 'Testing'
            && $request['city'] === 'Mumbai'
            && !isset($request['address']);
    });
});

test('readCreditHistory() sends post request with date range', function () {
    Http::fake([
        'https://api.hostpinnacle.test/account/readcredithistory' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->accountProfile()->readCreditHistory([
        'fromdate' => '2026-08-01',
        'todate' => '2026-08-31',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/account/readcredithistory'
            && $request->method() === 'POST'
            && $request['fromdate'] === '2026-08-01'
            && $request['todate'] === '2026-08-31';
    });
});

test('readCreditHistory() throws IsNullException when fromdate or todate missing', function () {
    (new Hostpinnacle())->accountProfile()->readCreditHistory(['fromdate' => '2026-08-01']);
})->throws(IsNullException::class, 'fromdate and todate must not be null');
