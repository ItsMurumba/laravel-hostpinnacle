<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;
use Workbench\App\Models\User;
use Workbench\Database\Factories\UserFactory;

beforeEach(function () {
    if (! Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    $this->artisan('migrate')->run();
});

function makeHostpinnacleUser(): User
{
    return UserFactory::new()->create();
}

function makeHostpinnacleAccount(int $ownerId, array $overrides = []): HostpinnacleAccount
{
    return HostpinnacleAccount::create(array_merge([
        'user_id' => $ownerId,
        'api_key' => 'test-api-key-12345',
        'sender_id' => 'SENDER',
        'username' => 'user',
        'password' => 'secret',
        'name' => 'Test Account',
    ], $overrides));
}

test('registers saas api and web routes when saas is enabled', function () {
    expect(Route::has('hostpinnacle.api.accounts.index'))->toBeTrue();
    expect(Route::has('hostpinnacle.web.accounts.index'))->toBeTrue();
});

test('index lists only the current owners accounts as json', function () {
    $user = makeHostpinnacleUser();
    $other = makeHostpinnacleUser();
    makeHostpinnacleAccount($user->id, ['name' => 'Mine']);
    makeHostpinnacleAccount($other->id, ['name' => 'Not mine']);

    $response = $this->actingAs($user)->getJson('/api/hostpinnacle/accounts');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Mine');
});

test('index requires authentication', function () {
    $this->getJson('/api/hostpinnacle/accounts')->assertStatus(401);
});

test('show returns a single owned account', function () {
    $user = makeHostpinnacleUser();
    $account = makeHostpinnacleAccount($user->id);

    $response = $this->actingAs($user)->getJson("/api/hostpinnacle/accounts/{$account->id}");

    $response->assertOk();
    $response->assertJsonPath('data.id', $account->id);
});

test('show forbids access to another owners account', function () {
    $user = makeHostpinnacleUser();
    $owner = makeHostpinnacleUser();
    $account = makeHostpinnacleAccount($owner->id);

    $this->actingAs($user)->getJson("/api/hostpinnacle/accounts/{$account->id}")->assertStatus(403);
});

test('store creates an account for the current owner', function () {
    $user = makeHostpinnacleUser();

    $response = $this->actingAs($user)->postJson('/api/hostpinnacle/accounts', [
        'api_key' => 'new-api-key-12345',
        'sender_id' => 'NEWSENDER',
        'username' => 'newuser',
        'password' => 'newpass',
        'name' => 'New Account',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.name', 'New Account');
    $this->assertDatabaseHas('hostpinnacle_accounts', [
        'name' => 'New Account',
        'user_id' => $user->id,
    ]);
});

test('store validates required fields', function () {
    $user = makeHostpinnacleUser();

    $this->actingAs($user)->postJson('/api/hostpinnacle/accounts', [])
        ->assertJsonValidationErrors(['api_key', 'sender_id', 'username', 'password']);
});

test('update modifies an owned account without requiring password', function () {
    $user = makeHostpinnacleUser();
    $account = makeHostpinnacleAccount($user->id);

    $response = $this->actingAs($user)->putJson("/api/hostpinnacle/accounts/{$account->id}", [
        'name' => 'Renamed',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Renamed');
});

test('update forbids modifying another owners account', function () {
    $user = makeHostpinnacleUser();
    $owner = makeHostpinnacleUser();
    $account = makeHostpinnacleAccount($owner->id);

    $this->actingAs($user)->putJson("/api/hostpinnacle/accounts/{$account->id}", ['name' => 'Hijacked'])
        ->assertStatus(403);
});

test('destroy deletes an owned account', function () {
    $user = makeHostpinnacleUser();
    $account = makeHostpinnacleAccount($user->id);

    $this->actingAs($user)->deleteJson("/api/hostpinnacle/accounts/{$account->id}")->assertOk();

    $this->assertDatabaseMissing('hostpinnacle_accounts', ['id' => $account->id]);
});

test('destroy forbids deleting another owners account', function () {
    $user = makeHostpinnacleUser();
    $owner = makeHostpinnacleUser();
    $account = makeHostpinnacleAccount($owner->id);

    $this->actingAs($user)->deleteJson("/api/hostpinnacle/accounts/{$account->id}")->assertStatus(403);

    $this->assertDatabaseHas('hostpinnacle_accounts', ['id' => $account->id]);
});
