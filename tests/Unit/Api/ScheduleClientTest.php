<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\ScheduleClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('schedule() returns a ScheduleClient', function () {
    $hostpinnacle = new Hostpinnacle();

    expect($hostpinnacle->schedule())->toBeInstanceOf(ScheduleClient::class);
});

test('read() sends post request with date range', function () {
    Http::fake([
        'https://api.hostpinnacle.test/schedule/read' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->schedule()->read([
        'fromdate' => '2026-08-01',
        'todate' => '2026-08-31',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/schedule/read'
            && $request->method() === 'POST'
            && $request['userid'] === 'testuser'
            && $request['password'] === 'testpass'
            && $request['fromdate'] === '2026-08-01'
            && $request['todate'] === '2026-08-31'
            && $request['output'] === 'json'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('read() throws IsNullException when fromdate or todate missing', function () {
    (new Hostpinnacle())->schedule()->read(['fromdate' => '2026-08-01']);
})->throws(IsNullException::class, 'fromdate and todate must not be null');

test('update() sends post request with uuid and scheduletime', function () {
    Http::fake([
        'https://api.hostpinnacle.test/schedule/update' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->schedule()->update([
        'uuid' => '1234',
        'scheduletime' => '2026-08-13 00:09',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/schedule/update'
            && $request->method() === 'POST'
            && $request['uuid'] === '1234'
            && $request['scheduletime'] === '2026-08-13 00:09';
    });
});

test('update() throws IsNullException when uuid or scheduletime missing', function () {
    (new Hostpinnacle())->schedule()->update(['uuid' => '1234']);
})->throws(IsNullException::class, 'uuid and scheduletime must not be null');

test('delete() sends post request with uuid', function () {
    Http::fake([
        'https://api.hostpinnacle.test/schedule/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->schedule()->delete(['uuid' => '1234']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/schedule/delete'
            && $request->method() === 'POST'
            && $request['uuid'] === '1234';
    });
});

test('delete() throws IsNullException when uuid missing', function () {
    (new Hostpinnacle())->schedule()->delete([]);
})->throws(IsNullException::class, 'uuid must not be null');
