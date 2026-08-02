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

    Http::assertSent(function ($request) {
        return str_contains($request->body(), "name=\"trackLink\"\r\nContent-Length: 4\r\n\r\ntrue\r\n")
            && str_contains($request->body(), "name=\"smartLinkTitle\"\r\nContent-Length: 15\r\n\r\nMy Example Link\r\n");
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
