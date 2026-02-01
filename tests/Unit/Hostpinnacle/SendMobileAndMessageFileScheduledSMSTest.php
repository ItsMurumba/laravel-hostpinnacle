<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('sends with file and scheduleTime', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $file = hostpinnacle_mock_file("Phone,Message\n254700000000,Hi\n");
    $hostpinnacle = new Hostpinnacle();
    $response = $hostpinnacle->sendMobileAndMessageFileScheduledSMS([
        'file' => $file,
        'scheduledTime' => '2025-02-04 14:00:00',
    ]);

    expect($response->successful())->toBeTrue();
    $file->close();
});

test('throws IsNullException when file is missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendMobileAndMessageFileScheduledSMS(['scheduledTime' => '2025-02-04 14:00:00']);
})->throws(IsNullException::class, 'file must not be null');
