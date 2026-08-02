<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\PasswordClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('password() returns a PasswordClient', function () {
    expect((new Hostpinnacle())->password())->toBeInstanceOf(PasswordClient::class);
});

test('change() sends post request with newpassword and confirmpassword', function () {
    Http::fake([
        'https://api.hostpinnacle.test/password/change' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->password()->change([
        'newpassword' => 'NewSecret123',
        'confirmpassword' => 'NewSecret123',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/password/change'
            && $request->method() === 'POST'
            && $request['newpassword'] === 'NewSecret123'
            && $request['confirmpassword'] === 'NewSecret123'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('change() throws IsNullException when newpassword or confirmpassword missing', function () {
    (new Hostpinnacle())->password()->change(['newpassword' => 'NewSecret123']);
})->throws(IsNullException::class, 'newpassword and confirmpassword must not be null');
