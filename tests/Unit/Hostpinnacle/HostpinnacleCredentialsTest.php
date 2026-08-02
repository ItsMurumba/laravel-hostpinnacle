<?php

use Illuminate\Http\Client\Response;
use Itsmurumba\Hostpinnacle\Hostpinnacle;
use Itsmurumba\Hostpinnacle\HostpinnacleCredentials;
use Illuminate\Support\Facades\Http;

test('fromArray accepts snake_case keys', function () {
    $credentials = HostpinnacleCredentials::fromArray([
        'api_key' => 'snake-key',
        'sender_id' => 'SNAKEID',
        'username' => 'snakeuser',
        'password' => 'snakepass',
        'base_url' => 'https://snake.test',
    ]);

    expect($credentials->getApiKey())->toBe('snake-key')
        ->and($credentials->getSenderId())->toBe('SNAKEID')
        ->and($credentials->getBaseUrl())->toBe('https://snake.test');
});

test('fromArray accepts camelCase keys', function () {
    $credentials = HostpinnacleCredentials::fromArray([
        'apiKey' => 'camel-key',
        'senderId' => 'CAMELID',
        'username' => 'cameluser',
        'password' => 'camelpass',
        'baseUrl' => 'https://camel.test',
    ]);

    expect($credentials->getApiKey())->toBe('camel-key')
        ->and($credentials->getSenderId())->toBe('CAMELID')
        ->and($credentials->getBaseUrl())->toBe('https://camel.test');
});

test('fromArray prefers snake_case over camelCase when both are given', function () {
    $credentials = HostpinnacleCredentials::fromArray([
        'api_key' => 'snake-key',
        'apiKey' => 'camel-key',
        'sender_id' => 'SNAKEID',
        'senderId' => 'CAMELID',
        'username' => 'user',
        'password' => 'pass',
        'base_url' => 'https://snake.test',
        'baseUrl' => 'https://camel.test',
    ]);

    expect($credentials->getApiKey())->toBe('snake-key')
        ->and($credentials->getSenderId())->toBe('SNAKEID')
        ->and($credentials->getBaseUrl())->toBe('https://snake.test');
});

test('fromArray treats an empty base_url as null', function () {
    $credentials = HostpinnacleCredentials::fromArray([
        'api_key' => 'key',
        'sender_id' => 'ID',
        'username' => 'user',
        'password' => 'pass',
        'base_url' => '',
    ]);

    expect($credentials->getBaseUrl())->toBeNull();
});

beforeEach(function () {
    hostpinnacle_set_config();
});

test('Hostpinnacle uses credentials from HostpinnacleCredentials when provided', function () {
    Http::fake([
        'https://custom.api.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $credentials = new HostpinnacleCredentials(
        'custom-api-key',
        'CUSTOMID',
        'customuser',
        'custompass',
        'https://custom.api.test'
    );

    $hostpinnacle = new Hostpinnacle($credentials);
    /** @var Response $response */
    $response = $hostpinnacle->sendQuickSMS([
        'msg' => 'Test',
        'mobile' => '254700000000',
    ]);

    expect($response)->not->toBeNull();
    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://custom.api.test/send')
            && $request->hasHeader('apikey', 'custom-api-key');
    });
});

test('Hostpinnacle uses config when constructed with null credentials', function () {
    Http::fake([
        'https://api.hostpinnacle.test/send' => Http::response(['status' => 'success'], 200),
    ]);

    $hostpinnacle = new Hostpinnacle();
    /** @var Response $response */
    $response = $hostpinnacle->sendQuickSMS([
        'msg' => 'Test',
        'mobile' => '254700000000',
    ]);

    expect($response)->not->toBeNull();
    expect($response->successful())->toBeTrue();
    Http::assertSent(function ($request) {
        return $request->hasHeader('apikey', 'test-api-key');
    });
});
