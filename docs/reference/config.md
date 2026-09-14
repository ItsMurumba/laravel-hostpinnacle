# Config reference

`config/hostpinnacle.php` (published via `php artisan hostpinnacle:install` or `vendor:publish --tag=hostpinnacle-config`).

## Single-account (env)

| Key | Env | Description |
|-----|-----|-------------|
| `apiKey` | `HOSTPINNACLE_API_KEY` | API key from Hostpinnacle Portal |
| `senderId` | `HOSTPINNACLE_SENDER_ID` | Sender ID from Hostpinnacle Portal |
| `username` | `HOSTPINNACLE_LOGIN_USERNAME` | Username for Hostpinnacle Portal |
| `password` | `HOSTPINNACLE_LOGIN_PASSWORD` | Password for Hostpinnacle Portal |
| `baseUrl` | `HOSTPINNACLE_BASE_URL` | Base URL for Hostpinnacle API |

## SaaS (`saas` array)

::: warning Table and owner column
`table`, `owner_key` and `owner_key_type` are used by the published migration. Set them before running `php artisan migrate`. Do not change them after the migration has run, or the model will expect a different table/column than exists in the database.
:::

| Key | Env | Default | Description |
|-----|-----|---------|-------------|
| `enabled` | `HOSTPINNACLE_SAAS_ENABLED` | `false` | Turn on multi-account (migrations, routes, `Hostpinnacle::for()`) |
| `table` | — | `hostpinnacle_accounts` | Table name for stored accounts (set before migrating; do not change after) |
| `owner_key` | `HOSTPINNACLE_SAAS_OWNER_KEY` | `user_id` | Owner foreign key column name (set before migrating; do not change after) |
| `owner_key_type` | `HOSTPINNACLE_SAAS_OWNER_KEY_TYPE` | `unsignedBigInteger` | Owner column type: `unsignedBigInteger`, `uuid`, or `string` (set before migrating; use `uuid` or `string` when owner model has UUID/string PK) |
| `owner_model` | `HOSTPINNACLE_SAAS_OWNER_MODEL` | `App\Models\User` | Eloquent model for owner relation |
| `api_routes_enabled` | `HOSTPINNACLE_SAAS_API_ROUTES_ENABLED` | `true` | Register API CRUD routes when SaaS enabled |
| `web_routes_enabled` | `HOSTPINNACLE_SAAS_WEB_ROUTES_ENABLED` | `true` | Register Web CRUD routes when SaaS enabled |
| `api_prefix` | `HOSTPINNACLE_SAAS_API_PREFIX` | `api` | API route prefix |
| `web_prefix` | `HOSTPINNACLE_SAAS_WEB_PREFIX` | `hostpinnacle` | Web route path prefix |
| `api_middleware` | — | `['api', 'auth:sanctum']` | Middleware for API routes |
| `web_middleware` | — | `['web', 'auth']` | Middleware for Web routes |
