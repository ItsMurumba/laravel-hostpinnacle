<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('sends post with scheduleTime', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    $response = $hostpinnacle->sendQuickScheduledSMS([
        'msg' => 'Scheduled message',
        'mobile' => '254711111111',
        'scheduledTime' => '2025-02-01 10:00:00',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request['scheduleTime'] === '2025-02-01 10:00:00';
    });
});

test('throws IsNullException when msg or mobile is missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendQuickScheduledSMS(['msg' => 'Hi']);
})->throws(IsNullException::class, 'msg and mobile must not be null');
