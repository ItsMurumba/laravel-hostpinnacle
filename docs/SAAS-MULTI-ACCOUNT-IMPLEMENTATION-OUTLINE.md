# Hostpinnacle Package: SaaS / Multi-Account Implementation Outline

This document outlines how to extend the Laravel Hostpinnacle package to support **both**:

1. **Single-account (current):** One set of credentials via env variables (`HOSTPINNACLE_*`), used app-wide. Ideal for single-tenant apps.
2. **Multi-account (SaaS):** Each customer/tenant configures their own Hostpinnacle credentials (via an UI that **you** build), stored in the database, and sends SMS from their own account. Ideal for SaaS where each customer has their own Hostpinnacle account.

The design keeps existing usage unchanged and adds optional, opt-in behaviour for SaaS. **Account management is exposed via both API and Web routes** (no UI shipped), so implementers can build their own interface and choose the style that fits:
- **API routes** — JSON in/out; for SPAs, mobile apps, or Vue/React front-ends that call the API (e.g. axios to `api/hostpinnacle/accounts`).
- **Web routes** — same CRUD actions under the `web` middleware; support both classic form submissions (redirects with flash messages) and AJAX/JSON (e.g. Laravel with inbuilt Vue posting to web routes with `Accept: application/json`). Ideal for Blade + Vue hybrid apps that prefer web middleware and session-based auth.
Optionally, example views can be provided later as a reference only.

---

## 1. Current Architecture (Reference)

| Component | Behaviour |
|-----------|-----------|
| **Config** (`config/hostpinnacle.php`) | Reads `apiKey`, `senderId`, `username`, `password`, `baseUrl` from env. |
| **Hostpinnacle class** | Constructor calls `setApiKey()`, `setBaseUrl()`, `setSenderId()`, `setUsername()`, `setPassword()` which all use `Config::get('hostpinnacle.*')`. No way to inject credentials. |
| **Service provider** | Binds `'laravel-hostpinnacle'` → `new Hostpinnacle()` (no args). |
| **Facade** | `Hostpinnacle` facade resolves to `'hostpinnacle'` (note: provider binds `'laravel-hostpinnacle'`; alignment should be verified). |
| **SMS methods** | All use `$this->apiKey`, `$this->baseUrl`, `$this->username`, `$this->password`, `$this->senderId` (from constructor). |

Credentials are therefore **global and fixed** per app instance. To support SaaS we need **per-request or per-tenant credentials** without breaking `new Hostpinnacle()` or `Hostpinnacle::sendQuickSMS(...)`.

---

## 2. Design Principles

- **Backward compatible:** Existing apps that use env + facade or `new Hostpinnacle()` continue to work with no code changes.
- **Explicit over implicit:** SaaS usage explicitly passes or resolves “which account” (e.g. `Hostpinnacle::for($account)`), rather than magic global state.
- **Package-agnostic tenancy:** The package does not assume “user” or “tenant”; it provides storage and a resolver that the app configures (e.g. by user, team, or tenant id).
- **API and Web routes, no UI in package:** Migration + model + controller(s) and **both API and Web** route groups for account CRUD. The package does **not** ship any UI; implementers build their own. **API routes** return JSON (for SPAs, mobile, or Vue/React calling the API). **Web routes** use `web` middleware and support classic form posts (redirect + flash) and AJAX (e.g. Laravel with inbuilt Vue posting with `Accept: application/json`). Config can enable API and/or Web routes separately. Optionally, the package can later provide *example* views as a reference only.

---

## 3. Proposed Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        Application                                       │
├─────────────────────────────────────────────────────────────────────────┤
│  Single-account (current)          │  Multi-account (SaaS)              │
│  • Hostpinnacle facade              │  • HostpinnacleAccount model      │
│  • new Hostpinnacle()               │  • Hostpinnacle::for($account)     │
│  • Credentials from Config (env)    │  • Credentials from DB             │
└─────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  Hostpinnacle class                                                     │
│  Constructor: Hostpinnacle(?HostpinnacleCredentials $credentials = null) │
│  • If $credentials === null → load from Config (current behaviour)      │
│  • If $credentials provided → use them (new behaviour)                  │
└─────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────┐
│  HostpinnacleCredentials (value object)                                  │
│  apiKey, senderId, username, password, baseUrl                           │
│  • From config array or from HostpinnacleAccount model                   │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Implementation Outline

### Phase 1: Credentials abstraction (no breaking changes)

**4.1 Introduce `HostpinnacleCredentials` (value object)**

- **Location:** `src/HostpinnacleCredentials.php` (or `src/Support/HostpinnacleCredentials.php`).
- **Purpose:** Encapsulate the five values (apiKey, senderId, username, password, baseUrl) so they can be passed into `Hostpinnacle` or created from config/DB.
- **API (suggestion):**
  - Constructor: `__construct(string $apiKey, string $senderId, string $username, string $password, ?string $baseUrl = null)`.
  - Optional: `static fromConfig(): self` (reads from `Config::get('hostpinnacle')`).
  - Optional: `static fromArray(array $array): self`.
  - Getters for each property (or public readonly properties in PHP 8+).
- **baseUrl:** If not provided, use a default from config or package constant so single-account behaviour is unchanged.

**4.2 Refactor `Hostpinnacle` to accept optional credentials**

- **Constructor:** `public function __construct(?HostpinnacleCredentials $credentials = null)`.
  - If `$credentials === null`: load credentials via `HostpinnacleCredentials::fromConfig()` (or equivalent) and assign to properties (current behaviour).
  - If `$credentials` is provided: use it to set `$this->apiKey`, `$this->baseUrl`, etc., and do **not** read from Config.
- **Internal:** Replace direct `Config::get(...)` in setters with logic that either uses the injected credentials or falls back to config (only when constructed without credentials). Prefer a single code path: “resolve credentials once in constructor.”
- **No change** to public SMS method signatures; they continue to use `$this->apiKey`, `$this->username`, etc.
- **Tests:** Existing tests that use `Config::set('hostpinnacle.*')` and `new Hostpinnacle()` must still pass. Add tests for `new Hostpinnacle($credentials)` using a `HostpinnacleCredentials` instance.

**4.3 Service provider and facade (single-account)**

- Keep binding that returns `new Hostpinnacle()` (no args) so the facade and `app('laravel-hostpinnacle')` (or `app('hostpinnacle')`) still use config-based credentials.
- Fix facade accessor if it currently points to `'hostpinnacle'` while the provider binds `'laravel-hostpinnacle'` so that both refer to the same binding and behaviour is consistent.

---

### Phase 2: Storage and model for SaaS accounts

**4.4 Migration: `hostpinnacle_accounts` table**

- **Purpose:** Store one Hostpinnacle “account” (credentials) per tenant/customer/user.
- **Suggested columns:**
  - `id` (bigInteger, primary key).
  - **Owner reference (configurable):** e.g. `user_id` (nullable), or a polymorphic `accountable_type` + `accountable_id`, or `tenant_id` — see 4.5.
  - `api_key` (string).
  - `sender_id` (string).
  - `username` (string).
  - `password` (string); consider encryption at rest (Laravel `encrypted` cast or custom).
  - `base_url` (string, nullable) — default from config if null.
  - `name` (string, nullable) — optional label, e.g. “Main account”.
  - `timestamps`.
- **Index:** Index owner columns (e.g. `user_id` or `accountable_type, accountable_id`) for “list accounts for current tenant” queries.
- **Publishing:** Migration published via `php artisan vendor:publish --tag=hostpinnacle-migrations`. Optional: run in package service provider if config says `hostpinnacle.saas.enabled` or similar.

**4.5 Model: `HostpinnacleAccount`**

- **Location:** `src/Models/HostpinnacleAccount.php` (or `src/HostpinnacleAccount.php`).
- **Relations:** BelongsTo user (or polymorphic “accountable”) as per migration. Relationship name configurable via config (e.g. `hostpinnacle.saas.owner_relation`).
- **Credentials:** Method `toCredentials(): HostpinnacleCredentials` that returns a value object from the model’s attributes (and decrypts password if needed).
- **Security:** Use `encrypted` cast for `password` (and optionally `api_key`) so DB stores encrypted values.
- **Config:** Config key, e.g. `hostpinnacle.saas.owner_key` (`user_id` vs `tenant_id` vs polymorphic) so the app can adapt without code change.

**4.6 Config additions**

- **File:** `config/hostpinnacle.php` (extend published config).
- **New keys (suggestion):**
  - `hostpinnacle.saas.enabled` (bool) — turn on SaaS features (migrations, routes, “for account” API).
  - `hostpinnacle.saas.owner_type` — e.g. `user` (then we use `user_id`), or `tenant`, or `polymorphic`.
  - `hostpinnacle.saas.table` — table name, default `hostpinnacle_accounts`.
  - `hostpinnacle.saas.encrypt_password` (bool) — whether to encrypt password in DB.
  - **Routes:** `saas.api_routes_enabled` (bool), `saas.web_routes_enabled` (bool); optional `saas.api_prefix`, `saas.web_prefix`, `saas.api_middleware`, `saas.web_middleware` so implementers can use API only, Web only, or both (e.g. API for SPAs, Web for Laravel with inbuilt Vue).

This keeps the package usable for different SaaS shapes (per-user, per-tenant, etc.) without hardcoding “User” in the package.

---

### Phase 3: Resolver / factory for “send as this account”

**4.7 Factory or static helper**

- **Option A – Factory class:** `HostpinnacleFactory` with method `for(HostpinnacleAccount|HostpinnacleCredentials $accountOrCredentials): Hostpinnacle`. Registered as singleton; creates a new `Hostpinnacle` instance with the given credentials each time (no shared state).
- **Option B – Facade method:** `Hostpinnacle::for(HostpinnacleAccount $account)` that returns a new `Hostpinnacle` instance configured with `$account->toCredentials()`. Internally this can call the same factory.
- **Recommendation:** Implement a small `HostpinnacleFactory` that both the facade and the app can use; facade exposes `Hostpinnacle::for($account)` for convenience.

**4.8 Usage in SaaS app (example)**

- **Store account:** User fills a form (API Key, Sender ID, Username, Password, Base URL); app saves to `hostpinnacle_accounts` with `user_id` (or tenant id, etc.).
- **Send SMS as that account:** In a controller, resolve the account (e.g. `auth()->user()->hostpinnacleAccount` or first active account), then:
  - `Hostpinnacle::for($account)->sendQuickSMS([...])`  
  or  
  - `app(HostpinnacleFactory::class)->for($account)->sendQuickSMS([...])`.
- **Single-account app:** No change; continue using `Hostpinnacle::sendQuickSMS([...])` or `app('hostpinnacle')->sendQuickSMS([...])` with env-based credentials.

---

### Phase 4: API and Web routes for managing accounts (no UI in package)

**4.9 Controller and shared logic**

- **Scope:** The package provides **both API and Web routes** for CRUD of Hostpinnacle accounts. No UI is shipped; implementers build their own (Blade, Vue, SPA, mobile, etc.) and call either the API or the web endpoints.
- **Controller:** e.g. `HostpinnacleAccountController` — `index`, `store`, `update`, `destroy` (and optionally `show`). Resolve “current owner” from config (e.g. `auth()->user()->id`). Validate input (required fields, format). Use policy or middleware so only the owner can manage their accounts.
- **Response format (single controller for both):**
  - When the request expects JSON (`Accept: application/json` or `request()->wantsJson()`): always return JSON (resource or array). Used by API routes and by AJAX calls from Vue/Blade (e.g. axios with `Accept: application/json`).
  - When the request is a normal web request (e.g. form POST from Blade): return `redirect()->back()->with('success', ...)` or `redirect()->route(...)` and use session flash for errors. So Laravel apps with inbuilt Vue can use classic forms that post to web routes and get redirects, or Vue components that call the same web routes and get JSON.
- **API resources:** Optional `HostpinnacleAccountResource` for consistent JSON; mask or omit sensitive fields (e.g. mask `api_key`, never return `password`).

**4.10 API routes**

- **Purpose:** For SPAs, mobile apps, or any client that calls a dedicated API (e.g. Vue/React app using axios to `api/...`).
- **Registration:** Only if `hostpinnacle.saas.enabled` and e.g. `hostpinnacle.saas.api_routes_enabled` are true. Prefix: `api` (or configurable). Middleware: e.g. `auth:sanctum` or `auth:api` (configurable).
- **Actions:** Same controller as web; routes point to same methods. Controller detects API route (or `wantsJson()`) and returns JSON.

**4.11 Web routes**

- **Purpose:** For Laravel apps using **web** stack (Blade, Laravel with inbuilt Vue, session-based auth). Supports (a) classic form submissions → redirect + flash, and (b) Vue/Blade AJAX (e.g. axios to same URL with `Accept: application/json`) → JSON response.
- **Registration:** Only if `hostpinnacle.saas.enabled` and e.g. `hostpinnacle.saas.web_routes_enabled` are true. Middleware: `web`, `auth` (or configurable). No `api` prefix; use a path prefix such as `hostpinnacle/accounts` (configurable).
- **Actions:** Same controller; store/update/destroy return redirect when not `wantsJson()`, else JSON.

**4.12 Config for routes**

- **Suggested keys:** `saas.api_routes_enabled` (bool), `saas.web_routes_enabled` (bool), optional `saas.api_prefix`, `saas.web_prefix`, `saas.api_middleware`, `saas.web_middleware`. Defaults: both enabled when SaaS is enabled; standard `api` and `web` middleware.

**4.13 Example UI (optional, out of scope for initial release)**

- The package **does not** ship production UI. If desired later, *example* views (e.g. simple Blade forms or Vue components) could be provided as a **reference only** — e.g. in a separate repo or as publishable “example” stubs (e.g. `hostpinnacle-example-views`) so implementers know they are optional. This keeps the core package lean and UI-agnostic.

---

### Phase 5: Documentation and backward compatibility

**4.14 README / docs**

- **Single-account (existing):** Keep current “Configurations” and “Usage” (env vars, `Hostpinnacle::sendQuickSMS(...)`). State that this is unchanged.
- **SaaS / multi-account:** New section:
  - Enable SaaS in config.
  - Run migration (`hostpinnacle_accounts`).
  - **API routes:** Use for SPAs, mobile, or Vue/React calling the API (JSON). Enable with `saas.api_routes_enabled`.
  - **Web routes:** Use for Laravel with inbuilt Vue/Blade (form posts → redirects, or AJAX with `Accept: application/json` → JSON). Enable with `saas.web_routes_enabled`. Ideal for non-API settings (session auth, Blade + Vue).
  - Use the package’s API or Web routes (or the model directly) for CRUD; build your own UI.
  - Resolve account (e.g. from current user) and call `Hostpinnacle::for($account)->sendQuickSMS(...)`.
  - Example: “Resolving account for current user” (e.g. `auth()->user()->hostpinnacleAccount` or `HostpinnacleAccount::forUser(auth()->id())->first()`).
- **Security:** Recommend HTTPS, encrypted storage for password (and optionally api_key), and not logging credentials.

**4.15 Backward compatibility checklist**

- [x] Existing app with only env set and no new config keys: no errors; same behaviour as today.
- [x] `new Hostpinnacle()` with no args: uses Config (env).
- [x] `Hostpinnacle::sendQuickSMS(...)` (facade, no “for”): uses default binding (env).
- [x] No new required env vars; no required new tables unless the app opts in to SaaS.
- [x] All existing tests pass; new tests for credentials object and “for account” path.

---

## 5. File / Component Summary

| Component | Action |
|-----------|--------|
| `HostpinnacleCredentials` | **New** — value object for the five credentials. |
| `Hostpinnacle` | **Refactor** — constructor accepts optional `?HostpinnacleCredentials`; internal resolution from config only when null. |
| `HostpinnacleAccount` model | **New** — Eloquent model, `toCredentials()`, encrypted password (optional). |
| Migration `hostpinnacle_accounts` | **New** — publishable; owner column(s) configurable. |
| `HostpinnacleFactory` | **New** — `for(Account|Credentials): Hostpinnacle`. |
| `HostpinnacleServiceProvider` | **Extend** — register factory; optionally register routes; fix facade binding if needed. |
| `Hostpinnacle` Facade | **Extend** — add `Hostpinnacle::for($account)` if desired. |
| Config `hostpinnacle.php` | **Extend** — add `saas.enabled`, `saas.owner_*`, `saas.table`, `saas.api_routes_enabled`, `saas.web_routes_enabled`, etc. |
| API and Web Controller / Routes / Resources | **New** — optional, config-gated; CRUD via **API** routes (JSON) and **Web** routes (redirect or JSON); no UI in package. |
| README / docs | **Update** — single-account unchanged; new SaaS section. |

---

## 6. Suggested Implementation Order

1. **Phase 1:** Credentials object + refactor `Hostpinnacle` constructor + tests. No new config keys or DB. Ensures zero breakage and sets the base for “per-account” usage.
2. **Phase 2:** Config additions, migration, `HostpinnacleAccount` model, `toCredentials()`.
3. **Phase 3:** Factory + `Hostpinnacle::for($account)` (or equivalent) and a minimal test sending SMS with a stored account.
4. **Phase 4:** Controller (shared logic), **API routes** and **Web routes** for account CRUD, API resources; gate behind config (api_routes_enabled, web_routes_enabled). No UI in package.
5. **Phase 5:** README and any extra docs; final backward-compatibility pass and tests.

---

## 7. Security Considerations

- **Passwords (and optionally API keys)** in `hostpinnacle_accounts`: store encrypted (Laravel `encrypted` cast or `Crypt::encryptString`). Decrypt only when building `HostpinnacleCredentials` for sending.
- **API:** Validate and sanitise all inputs; use Laravel validation rules. Never expose raw credentials in API responses (e.g. mask `api_key` in index/show, omit password).
- **Authorization:** Only the “owner” (user/tenant) of an account can update or delete it; enforce in controller and, if applicable, policy.
- **HTTPS:** Document that production should use HTTPS so credentials are not sent in clear text.

---

This outline should be enough to implement single-account and SaaS multi-account support in a backward-compatible way. Once this is agreed, implementation can proceed phase by phase with tests at each step.
