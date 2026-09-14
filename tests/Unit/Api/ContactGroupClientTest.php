<?php

use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Api\ContactGroupClient;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    hostpinnacle_set_config();
});

test('contactGroups() returns a ContactGroupClient', function () {
    expect((new Hostpinnacle())->contactGroups())->toBeInstanceOf(ContactGroupClient::class);
});

test('create() sends post request with groupname', function () {
    Http::fake([
        'https://api.hostpinnacle.test/group/create' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contactGroups()->create(['groupname' => 'myNewGroup']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/group/create'
            && $request->method() === 'POST'
            && $request['groupname'] === 'myNewGroup'
            && $request->hasHeader('apikey', 'test-api-key');
    });
});

test('create() throws IsNullException when groupname is missing', function () {
    (new Hostpinnacle())->contactGroups()->create([]);
})->throws(IsNullException::class, 'groupname must not be null');

test('read() sends get request', function () {
    Http::fake([
        'https://api.hostpinnacle.test/group/read*' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contactGroups()->read();

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'https://api.hostpinnacle.test/group/read');
    });
});

test('update() sends post request with groupname and id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/group/update' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contactGroups()->update(['groupname' => 'updateGroupName', 'id' => '10']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/group/update'
            && $request->method() === 'POST'
            && $request['groupname'] === 'updateGroupName'
            && $request['id'] === '10';
    });
});

test('update() throws IsNullException when groupname or id missing', function () {
    (new Hostpinnacle())->contactGroups()->update(['groupname' => 'updateGroupName']);
})->throws(IsNullException::class, 'groupname and id must not be null');

test('delete() sends post request with id', function () {
    Http::fake([
        'https://api.hostpinnacle.test/group/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contactGroups()->delete(['id' => '10']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.hostpinnacle.test/group/delete'
            && $request->method() === 'POST'
            && $request['id'] === '10';
    });
});

test('delete() sends post request with groupname', function () {
    Http::fake([
        'https://api.hostpinnacle.test/group/delete' => Http::response(['status' => 'success'], 200),
    ]);

    $response = (new Hostpinnacle())->contactGroups()->delete(['groupname' => 'group1,group2,group3']);

    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request['groupname'] === 'group1,group2,group3' && !isset($request['id']);
    });
});

test('delete() throws IsNullException when both id and groupname are missing', function () {
    (new Hostpinnacle())->contactGroups()->delete([]);
})->throws(IsNullException::class, 'id or groupname must not be null');
