<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('sends post request with scheduleTime', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send*' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    $response = $hostpinnacle->sendGroupScheduledSMS([
        'msg' => 'Scheduled group msg',
        'groupIds' => '1,2',
        'scheduledTime' => '2025-02-02 12:00:00',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request['scheduleTime'] === '2025-02-02 12:00:00';
    });
});

test('throws IsNullException when required params missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendGroupScheduledSMS(['msg' => 'Only message']);
})->throws(IsNullException::class, 'msg and groupIds must not be null');
