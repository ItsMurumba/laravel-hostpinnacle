<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('sends with scheduleTime', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $file = hostpinnacle_mock_file("Phone\n254700000000\n");
    $hostpinnacle = new Hostpinnacle();
    $response = $hostpinnacle->sendMobileOnlyFileScheduledSMS([
        'msg' => 'Scheduled bulk',
        'file' => $file,
        'scheduledTime' => '2025-02-03 09:00:00',
    ]);

    expect($response->successful())->toBeTrue();
    $file->close();
});
