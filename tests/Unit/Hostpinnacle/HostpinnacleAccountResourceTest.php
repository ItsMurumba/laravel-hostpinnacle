<?php

use Illuminate\Http\Request;
use Itsmurumba\Hostpinnacle\Http\Resources\HostpinnacleAccountResource;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;

test('toArray masks the api_key and never exposes the password', function () {
    $account = new HostpinnacleAccount([
        'api_key' => 'abcd1234wxyz',
        'sender_id' => 'SENDER',
        'username' => 'user',
        'password' => 'secret',
        'base_url' => 'https://api.test',
        'name' => 'Test Account',
    ]);

    $array = (new HostpinnacleAccountResource($account))->toArray(new Request());

    expect($array['api_key'])->toBe('abcd****wxyz')
        ->and($array['name'])->toBe('Test Account')
        ->and($array['sender_id'])->toBe('SENDER')
        ->and($array)->not->toHaveKey('password');
});

test('toArray fully masks a short api_key', function () {
    $account = new HostpinnacleAccount([
        'api_key' => 'short',
        'sender_id' => 'SENDER',
        'username' => 'user',
        'password' => 'secret',
    ]);

    $array = (new HostpinnacleAccountResource($account))->toArray(new Request());

    expect($array['api_key'])->toBe('*****');
});

test('toArray returns null api_key when none is set', function () {
    $account = new HostpinnacleAccount([
        'api_key' => '',
        'sender_id' => 'SENDER',
        'username' => 'user',
        'password' => 'secret',
    ]);

    $array = (new HostpinnacleAccountResource($account))->toArray(new Request());

    expect($array['api_key'])->toBeNull();
});
