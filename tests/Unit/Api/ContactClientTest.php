<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\ContactClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('contacts() returns a ContactClient', function () {
    expect((new Hostpinnacle())->contacts())->toBeInstanceOf(ContactClient::class);
});

test('create() sends post request with contactname and mobileno', function () {
    Http::fake([
        'https://api.hostpinnacle.test/contact/create' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contacts()->create([
        'contactname' => 'TEST NAME',
        'mobileno' => '911232987678',
        'groupid' => '1',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/contact/create'
            && $request->method() === 'POST'
            && $request['contactname'] === 'TEST NAME'
            && $request['mobileno'] === '911232987678'
            && $request['groupid'] === '1'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('create() throws IsNullException when contactname or mobileno missing', function () {
    (new Hostpinnacle())->contacts()->create(['contactname' => 'TEST NAME']);
})->throws(IsNullException::class, 'contactname and mobileno must not be null');

test('upload() sends post request with contactname and mobileno', function () {
    Http::fake([
        'https://api.hostpinnacle.test/contact/upload' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contacts()->upload([
        'contactname' => 'TEST NAME',
        'mobileno' => '919999999999',
        'groupname' => 'myGroup',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/contact/upload'
            && $request->method() === 'POST'
            && $request['contactname'] === 'TEST NAME'
            && $request['mobileno'] === '919999999999'
            && $request['groupname'] === 'myGroup';
    });
});

test('upload() throws IsNullException when contactname or mobileno missing', function () {
    (new Hostpinnacle())->contacts()->upload(['mobileno' => '919999999999']);
})->throws(IsNullException::class, 'contactname and mobileno must not be null');

test('read() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/contact/read*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contacts()->read();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/contact/read');
    });
});

test('update() sends post request with id, contactname and mobileno', function () {
    Http::fake([
        'https://api.hostpinnacle.test/contact/update' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contacts()->update([
        'id' => '7',
        'contactname' => 'TEST NAME',
        'mobileno' => '919999999999',
        'groupid' => '1',
    ]);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/contact/update'
            && $request->method() === 'POST'
            && $request['id'] === '7'
            && $request['contactname'] === 'TEST NAME'
            && $request['mobileno'] === '919999999999'
            && $request['groupid'] === '1';
    });
});

test('update() throws IsNullException when id, contactname, or mobileno missing', function () {
    (new Hostpinnacle())->contacts()->update(['contactname' => 'TEST NAME', 'mobileno' => '919999999999']);
})->throws(IsNullException::class, 'id, contactname and mobileno must not be null');

test('delete() sends post request with id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/contact/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contacts()->delete(['id' => '4,5,1']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/contact/delete'
            && $request->method() === 'POST'
            && $request['id'] === '4,5,1';
    });
});

test('delete() throws IsNullException when id missing', function () {
    (new Hostpinnacle())->contacts()->delete([]);
})->throws(IsNullException::class, 'id must not be null');
