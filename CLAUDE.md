# CLAUDE.md

Guidance for Claude Code (and other agents) working in this repo.

## What this is

A Laravel package (`itsmurumba/laravel-hostpinnacle`) that wraps the Hostpinnacle SMS API. Two usage modes:

- **Single-account** (default): credentials come from `config/hostpinnacle.php` / `.env`. One set of Hostpinnacle credentials for the whole app.
- **SaaS multi-account** (opt-in via `hostpinnacle.saas.enabled`): each tenant/user stores their own credentials in the `hostpinnacle_accounts` table and sends SMS through their own account.

Full usage docs live in `docs/` (VitePress site); package/contribution rules are in `Contribution.md`. This file is about *how to work on the package itself*, not how to consume it — see those for the latter.

## Architecture map

```
Hostpinnacle              SMS client: sendQuickSMS, sendGroupSMS, file-upload SMS, scheduled variants.
                           Built from HostpinnacleCredentials; uses Illuminate's Http facade to call the API.

HostpinnacleCredentials    Value object holding apiKey/senderId/username/password/baseUrl.
                           ::fromConfig() reads config/hostpinnacle.php (single-account).
                           ::fromArray() reads a snake_case or camelCase array (SaaS: from a HostpinnacleAccount).

HostpinnacleFactory        ->for(HostpinnacleAccount|HostpinnacleCredentials): Hostpinnacle
                           Turns a stored account (or a raw credentials object) into a ready-to-use client.

Facades\Hostpinnacle       Static facade. Hostpinnacle::sendQuickSMS(...) uses the single-account singleton.
                           Hostpinnacle::for($account)->sendQuickSMS(...) is the SaaS multi-account entry point.

Models\HostpinnacleAccount SaaS storage: one row per tenant's credentials. ->toCredentials() bridges to
                           HostpinnacleCredentials. Table name, owner column name/type all come from config
                           (see below) — set before running the migration, not after.

Http\Controllers\HostpinnacleAccountController
                           CRUD for HostpinnacleAccount, scoped to the authenticated owner (Auth::user()).
                           Only registered when hostpinnacle.saas.enabled is true (see HostpinnacleServiceProvider::boot).
                           Enforces its own 401 (no user) / 403 (wrong owner) — not relying solely on route middleware.

Http\Resources\HostpinnacleAccountResource
                           JSON shape for an account. Masks api_key (first 4 / last 4 chars), never returns password.
```

## Conventions

- **PSR-2** coding style (per `Contribution.md`).
- **Pest**, not raw PHPUnit classes: `test('description', function () { ... });` + `expect()`. See any file under `tests/Unit/Hostpinnacle/` for the pattern.
- **`Illuminate\Support\Facades\Http`** for all outbound API calls — not Guzzle directly, even though Guzzle is a transitive dependency (via `illuminate/http`). Don't add `guzzlehttp/guzzle` back as a direct dependency; it was removed as dead weight (nothing in `src/` used it directly).
- **Config-driven feature flags with defaults inline**: every `config('hostpinnacle.saas.x', $default)` call repeats the same default as `config/hostpinnacle.php`. If you add a new SaaS config key, follow this pattern rather than assuming the config array is fully populated — see "Testing gotchas" below for why.
- `IsNullException` extends `InvalidArgumentException` (not a bare `Exception`) — required-argument-missing is exactly what that stdlib type models.

## Commands

- `composer test` / `vendor/bin/pest` — run the suite.
- `composer test:coverage` / `vendor/bin/pest --coverage` — needs PCOV or Xdebug (see `docs/guide/testing.md`); reports to `build/coverage/`.
- `php artisan hostpinnacle:install` — publishes `config/hostpinnacle.php` (asks before overwriting).

## Testing patterns

- **Http::fake() for the SMS client** — `tests/Unit/Hostpinnacle/SendQuickSMSTest.php` is the canonical example: fake the endpoint, call the method, assert on `Http::assertSent(...)`.
- **SaaS model/credentials tests** — `tests/Unit/Hostpinnacle/HostpinnacleAccountTest.php`: build a `HostpinnacleAccount` in memory (no DB needed) and assert `toCredentials()`/relationship behavior.
- **SaaS HTTP/route tests** (`tests/Feature/`) run under `Itsmurumba\Hostpinnacle\Tests\Support\SaasEnabledTestCase`, wired in `tests/Pest.php` via `uses(SaasEnabledTestCase::class)->in('Feature')`. That TestCase flips `hostpinnacle.saas.enabled` on in `getEnvironmentSetUp()` — config changes made later in a test's `beforeEach()` are too late to affect route registration, since `HostpinnacleServiceProvider::boot()` (which conditionally registers routes) runs during app bootstrap, before `beforeEach()` executes. It also drops `auth:sanctum`/`auth` from the route middleware since Sanctum isn't a project dependency; the controller enforces its own 401/403 via `Auth::user()`, so route-level auth middleware isn't required to test that.
- **Pest's `uses()` is one-per-directory**: only the root `tests/Pest.php` is loaded (nested `Pest.php` files in subdirectories are not), and a file can't mix two unrelated concrete TestCase classes. If a new test subtree needs a different TestCase, add another `uses(X::class)->in('SomeDir')` line to `tests/Pest.php` rather than dropping a `Pest.php` into that subdirectory.
- **DB-backed Feature tests**: `tests/TestCase.php` configures an in-memory `testing` sqlite connection. The `users` table isn't provided by any registered migration path in this package's test setup (no `laravel/sanctum`, no workbench migrations wired in) — `tests/Feature/HostpinnacleAccountControllerTest.php` creates it inline with `Schema::create` in `beforeEach()` when missing. Reuse that pattern rather than reaching for `RefreshDatabase`/factories tied to workbench discovery, which isn't set up here.
