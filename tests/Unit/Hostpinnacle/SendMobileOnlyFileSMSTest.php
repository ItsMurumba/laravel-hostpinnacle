<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('sends post with file attachment', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $file = hostpinnacle_mock_file("Phone\n254700000000\n");
    $hostpinnacle = new Hostpinnacle();
    $response = $hostpinnacle->sendMobileOnlyFileSMS([
        'msg' => 'Bulk message',
        'file' => $file,
    ]);

    expect($response->successful())->toBeTrue();
    $file->close();
});

test('sends trackLink and smartLinkTitle when provided', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $file = hostpinnacle_mock_file("Phone\n254700000000\n");
    $hostpinnacle = new Hostpinnacle();
    $hostpinnacle->sendMobileOnlyFileSMS([
        'msg' => 'Check out https://example.com',
        'file' => $file,
        'trackLink' => 'true',
        'smartLinkTitle' => 'My Example Link',
    ]);
    $file->close();

    // Matches the field's Content-Disposition line through to its value, tolerating an
    // optional Content-Length header line in between — Guzzle's multipart encoder emits
    // one on some versions and not others, but that's an encoder detail, not our concern.
    Http::assertSent(function ($request) {
        return preg_match('/name="trackLink"\r\n(?:[^\r\n]+\r\n)*\r\ntrue\r\n/', $request->body()) === 1
            && preg_match('/name="smartLinkTitle"\r\n(?:[^\r\n]+\r\n)*\r\nMy Example Link\r\n/', $request->body()) === 1;
    });
});

test('throws IsNullException when msg is missing', function () {
    $hostpinnacle = new Hostpinnacle();
    $file = new class {
        public function getClientOriginalExtension()
        {
            return 'csv';
        }
    };

    $hostpinnacle->sendMobileOnlyFileSMS(['file' => $file]);
})->throws(IsNullException::class, 'msg and file must not be null');

test('throws IsNullException when file is missing', function () {
    $hostpinnacle = new Hostpinnacle();

    $hostpinnacle->sendMobileOnlyFileSMS(['msg' => 'No file']);
})->throws(IsNullException::class, 'msg and file must not be null');
