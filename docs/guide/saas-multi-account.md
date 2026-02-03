# SaaS / Multi-Account

If you run a SaaS where each customer has their own Hostpinnacle account, you can enable **multi-account** mode. Each tenant/user stores their own credentials in the database and sends SMS using their account. Single-account usage is unchanged.

## Enable SaaS

1. **Config:** In `config/hostpinnacle.php`, set `saas.enabled` to `true` (or set `HOSTPINNACLE_SAAS_ENABLED=true` in `.env`).

2. **Migration:** Publish and run the accounts table:

   ```bash
   php artisan vendor:publish --tag=hostpinnacle-migrations
   php artisan migrate
   ```

3. **Routes (optional):** The package registers **API** and **Web** routes for CRUD on Hostpinnacle accounts when SaaS is enabled. You can turn them on/off in config:
   - **API routes** — JSON; for SPAs, mobile apps, or Vue/React calling the API (e.g. `api/hostpinnacle/accounts`). Use `saas.api_routes_enabled` and middleware such as `auth:sanctum`.
   - **Web routes** — same CRUD under `web` + `auth`; support form submissions (redirect + flash) or AJAX with `Accept: application/json`. Use `saas.web_routes_enabled`. Path prefix defaults to `hostpinnacle` (e.g. `hostpinnacle/accounts`).

   Build your own UI (Blade, Vue, SPA, etc.) and call these endpoints or use the `HostpinnacleAccount` model directly.

## Sending SMS as a stored account

Resolve the account (e.g. for the current user or tenant), then use the facade:

```php
use Itsmurumba\Hostpinnacle\Facades\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;

$account = HostpinnacleAccount::where('user_id', auth()->id())->first(); // or your owner key
$response = Hostpinnacle::for($account)->sendQuickSMS(['mobile' => '254720xxxxxx', 'msg' => 'Hello!']);
```

You can also pass `HostpinnacleCredentials` to `Hostpinnacle::for($credentials)` if you have a value object instead of a model.

## Config options (SaaS)

See [Config reference](/reference/config) for the full list. Key options:

| Key | Description |
|-----|-------------|
| `saas.enabled` | Turn on multi-account features (migrations, routes, `Hostpinnacle::for()`). |
| `saas.table` | Table name for accounts (default: `hostpinnacle_accounts`). |
| `saas.owner_type` / `saas.owner_key` / `saas.owner_model` | Owner of an account (e.g. `user`, `user_id`, `App\Models\User`). |
| `saas.encrypt_password` | Encrypt password in DB (default: true). |
| `saas.api_routes_enabled` / `saas.web_routes_enabled` | Enable API and/or Web CRUD routes. |
| `saas.api_prefix` / `saas.web_prefix` | Route prefixes (e.g. `api`, `hostpinnacle`). |
| `saas.api_middleware` / `saas.web_middleware` | Middleware for API and Web routes. |

## Security (SaaS)

- Use **HTTPS** in production so credentials are not sent in clear text.
- Passwords (and optionally API keys) are stored encrypted when `saas.encrypt_password` is true.
- Do not log credentials; the package masks `api_key` in API responses and never returns `password`.
