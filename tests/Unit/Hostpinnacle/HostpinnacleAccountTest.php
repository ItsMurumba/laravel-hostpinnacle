<?php

use Itsmurumba\Hostpinnacle\HostpinnacleCredentials;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;

beforeEach(function () {
    config()->set('hostpinnacle.saas.table', 'hostpinnacle_accounts');
    config()->set('hostpinnacle.saas.owner_key', 'user_id');
});

test('HostpinnacleAccount toCredentials returns HostpinnacleCredentials with model attributes', function () {
    $account = new HostpinnacleAccount([
        'api_key' => 'test-api-key',
        'sender_id' => 'SENDER',
        'username' => 'user',
        'password' => 'secret',
        'base_url' => 'https://api.test',
        'name' => 'Test Account',
    ]);

    $credentials = $account->toCredentials();

    expect($credentials)->toBeInstanceOf(HostpinnacleCredentials::class)
        ->and($credentials->getApiKey())->toBe('test-api-key')
        ->and($credentials->getSenderId())->toBe('SENDER')
        ->and($credentials->getUsername())->toBe('user')
        ->and($credentials->getPassword())->toBe('secret')
        ->and($credentials->getBaseUrl())->toBe('https://api.test');
});

test('HostpinnacleAccount toCredentials works with null base_url', function () {
    $account = new HostpinnacleAccount([
        'api_key' => 'key',
        'sender_id' => 'ID',
        'username' => 'u',
        'password' => 'p',
        'base_url' => null,
    ]);

    $credentials = $account->toCredentials();

    expect($credentials->getBaseUrl())->toBeNull();
});

test('HostpinnacleAccount owner relationship uses config owner_key and owner_model', function () {
    config()->set('hostpinnacle.saas.owner_model', 'Workbench\\App\\Models\\User');

    $account = new HostpinnacleAccount();
    $relation = $account->owner();

    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    $ownerKey = config('hostpinnacle.saas.owner_key', 'user_id');
    expect($relation->getForeignKeyName())->toBe($ownerKey);
});
