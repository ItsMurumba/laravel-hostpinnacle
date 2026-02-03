## laravel-hostpinnacle
Official laravel package for Hostpinnacle SMS Service(API). It includes all public available endpoints:
* Quick SMS
* Group SMS
* File Upload

## Installation

Run the following command to install Laravel Hostpinnacle package in your project:

````
composer require itsmurumba/laravel-hostpinnacle
````

If you are using **Laravel 5.5** and above, skip to the [**Configurations**](https://github.com/ItsMurumba/laravel-hostpinnacle#configurations) step.

After running the composer require above, you should add a service provider and alias of the package in config/app.php file.(For Laravel 5.4 and below)

````
Itsmurumba\Hostpinnacle\HostpinnacleServiceProvider::class
````

# Configurations

After installing the package, run the following command to install `hostpinnacle.php` configuartion file in the `config` folder:

````
php artisan hostpinnacle:install
````

or 
````
php artisan vendor:publish
````

Add and define the following variables in your `.env` file

````
HOSTPINNACLE_API_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxx
HOSTPINNACLE_SENDER_ID=xxxxxxxxxx
HOSTPINNACLE_LOGIN_USERNAME=xxxxxxxxxxxxxxx
HOSTPINNACLE_LOGIN_PASSWORD=xxxxxx
HOSTPINNACLE_BASE_URL=https://smsportal.hostpinnacle.co.ke/SMSApi
````

## Usage
Add the following constructor inside your controller:
`````
protected $hostpinnacle;

public function __construct(){
    $this->hostpinnacle = new Hostpinnacle();
}
`````
**1. Quick SMS**

a. Sending a quick SMS (Batch)
````
$data['mobile'] = '254720xxxxxx';
$data['msg'] = 'Hello World!';

$response = $this->hostpinnacle->sendQuickSMS($data);
````

b. Sending a quick scheduled SMS (Batch)
````
$data['scheduledTime'] = '2023-02-28 17:32:03';
$data['mobile'] = '254720xxxxxx';
$data['msg'] = 'Hello World!';

$response = $this->hostpinnacle->sendQuickScheduledSMS($data);
````

**2. Group SMS**

a. Sending a Group SMS
````
$data['groupIds'] = '1056';
$data['msg'] = 'Hello World!';

$response = $this->hostpinnacle->sendGroupSMS($data);
````

b. Sending a Group Scheduled SMS
````
$data['scheduledTime'] = '2023-02-28 17:32:03';
$data['groupIds'] = '1056';
$data['msg'] = 'Hello World!';

$response = $this->hostpinnacle->sendGroupScheduledSMS($data);
````
**3. File Upload SMS**

a. Sending an SMS from File with Mobile Numbers only
````
$data['file'] = $request->file('file');
$data['msg'] = 'Hello World!';

$response = $this->hostpinnacle->sendMobileOnlyFileSMS($data);
````

b. Sending an SMS from File with Mobile Numbers only Scheduled
````
$data['scheduledTime'] = '2023-02-28 17:32:03';
$data['file'] = $request->file('file');
$data['msg'] = 'Hello World !';

$response = $this->hostpinnacle->sendMobileOnlyFileScheduledSMS($data);
````
c. Sending an SMS from File with Mobile Numbers and message
````
$data['file'] = $request->file('file');

$response = $this->hostpinnacle->sendMobileAndMessageFileSMS($data);
````

d. Sending an SMS from File with Mobile Numbers and message Scheduled
````
$data['scheduledTime'] = '2023-02-28 17:32:03';
$data['file'] = $request->file('file');

$response = $this->hostpinnacle->sendMobileAndMessageFileScheduledSMS($data);
````

You can also use the **facade** (single-account, env-based credentials):

````
use Itsmurumba\Hostpinnacle\Facades\Hostpinnacle;

$response = Hostpinnacle::sendQuickSMS(['mobile' => '254720xxxxxx', 'msg' => 'Hello World!']);
````

---

## SaaS / Multi-Account (optional)

If you run a SaaS where each customer has their own Hostpinnacle account, you can enable **multi-account** mode. Each tenant/user stores their own credentials in the database and sends SMS using their account. Single-account usage above is unchanged.

### Enable SaaS

1. **Config:** In `config/hostpinnacle.php`, set `saas.enabled` to `true` (or set `HOSTPINNACLE_SAAS_ENABLED=true` in `.env`).

2. **Migration:** Publish and run the accounts table:
   ````
   php artisan vendor:publish --tag=hostpinnacle-migrations
   php artisan migrate
   ````

3. **Routes (optional):** The package registers **API** and **Web** routes for CRUD on Hostpinnacle accounts when SaaS is enabled. You can turn them on/off in config:
   - **API routes** — JSON; for SPAs, mobile apps, or Vue/React calling the API (e.g. `api/hostpinnacle/accounts`). Use `saas.api_routes_enabled` and middleware such as `auth:sanctum`.
   - **Web routes** — same CRUD under `web` + `auth`; support form submissions (redirect + flash) or AJAX with `Accept: application/json`. Use `saas.web_routes_enabled`. Path prefix defaults to `hostpinnacle` (e.g. `hostpinnacle/accounts`).

   Build your own UI (Blade, Vue, SPA, etc.) and call these endpoints or use the `HostpinnacleAccount` model directly.

### Sending SMS as a stored account

Resolve the account (e.g. for the current user or tenant), then use the facade:

````
use Itsmurumba\Hostpinnacle\Facades\Hostpinnacle;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;

$account = HostpinnacleAccount::where('user_id', auth()->id())->first(); // or your owner key
$response = Hostpinnacle::for($account)->sendQuickSMS(['mobile' => '254720xxxxxx', 'msg' => 'Hello!']);
````

You can also pass `HostpinnacleCredentials` to `Hostpinnacle::for($credentials)` if you have a value object instead of a model.

### Config options (SaaS)

| Key | Description |
|-----|-------------|
| `saas.enabled` | Turn on multi-account features (migrations, routes, `Hostpinnacle::for()`). |
| `saas.table` | Table name for accounts (default: `hostpinnacle_accounts`). |
| `saas.owner_type` / `saas.owner_key` / `saas.owner_model` | Owner of an account (e.g. `user`, `user_id`, `App\Models\User`). |
| `saas.encrypt_password` | Encrypt password in DB (default: true). |
| `saas.api_routes_enabled` / `saas.web_routes_enabled` | Enable API and/or Web CRUD routes. |
| `saas.api_prefix` / `saas.web_prefix` | Route prefixes (e.g. `api`, `hostpinnacle`). |
| `saas.api_middleware` / `saas.web_middleware` | Middleware for API and Web routes. |

### Security (SaaS)

- Use **HTTPS** in production so credentials are not sent in clear text.
- Passwords (and optionally API keys) are stored encrypted when `saas.encrypt_password` is true.
- Do not log credentials; the package masks `api_key` in API responses and never returns `password`.

---

# Testing

Tests use **[Pest](https://pestphp.com/)** (PHP testing framework) and **[Orchestra Testbench](https://orchestraplatform.com/docs/testbench)** for Laravel package testing.

Run the test suite:

```bash
composer test
```

Or directly with Pest:

```bash
vendor/bin/pest
```

**Code coverage** (optional) requires a coverage driver: **PCOV** or **Xdebug**. Without one, `composer test:coverage` will report "No code coverage driver is available".

- **PCOV** (line coverage, fast):  
  - **macOS (Homebrew):** `brew tap shivammathur/extensions && brew install pcov@8.3` (use your PHP version, e.g. `pcov@8.3`). Then run coverage with that PHP: `PATH="/opt/homebrew/opt/php/bin:$PATH" composer test:coverage`.  
  - **PECL:** `pecl install pcov` then add `extension=pcov.so` to your `php.ini`.
- **Xdebug** (full metrics): install Xdebug and enable [coverage mode](https://xdebug.org/docs/code_coverage#mode).

If you use **Laravel Herd**, either enable PCOV or Xdebug in Herd’s PHP settings, or run coverage with a PHP that has PCOV (e.g. Homebrew as above).

Then run:

```bash
composer test:coverage
```

Or with Pest:

```bash
vendor/bin/pest --coverage
```

Coverage is reported in the terminal and written to:

- **HTML:** `build/coverage/index.html` (open in a browser)
- **Clover XML:** `build/coverage/clover.xml` (for CI tools)

The `build/` directory is gitignored. The `test:coverage` script creates `build/coverage` automatically.

# Contribution
This is a community package and thus welcome anyone intrested to contribute in improving the package. Kindly go through the [Contribution.md](Contribution.md) before starting to contribute. Keep those PRs and Issues coming.

# Buy Me Coffee
Give this repo a star and i will have my super powers recharged. You can also follow me on twitter [@ItsMurumba](https://twitter.com/ItsMurumba)

# License
This package is licensed under the MIT License. Please review the [License](LICENSE.md) file for details
