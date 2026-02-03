# Laravel Hostpinnacle

Official Laravel package for the **Hostpinnacle SMS API**. It supports all public endpoints:

- **Quick SMS** — single and batch, with optional scheduling
- **Group SMS** — send to groups, with optional scheduling
- **File Upload** — SMS from file (mobile-only or mobile + message)

Use one set of credentials from env (single-account) or enable **SaaS multi-account** so each tenant stores their own credentials and sends via `Hostpinnacle::for($account)`.

## Quick start

```bash
composer require itsmurumba/laravel-hostpinnacle
```

```bash
php artisan hostpinnacle:install
```

Add your credentials to `.env`, then send SMS:

```php
use Itsmurumba\Hostpinnacle\Facades\Hostpinnacle;

$response = Hostpinnacle::sendQuickSMS([
    'mobile' => '254720xxxxxx',
    'msg' => 'Hello World!',
]);
```

## Documentation

- **[Installation](/guide/installation)** — Install the package and publish config
- **[Configuration](/guide/configuration)** — Env variables and config file
- **[Usage](/guide/usage)** — Quick SMS, Group SMS, File Upload (single-account)
- **[SaaS / Multi-Account](/guide/saas-multi-account)** — Per-tenant credentials and routes
- **[Testing](/guide/testing)** — Running tests and code coverage

## Links

- [GitHub](https://github.com/ItsMurumba/laravel-hostpinnacle)
- [Contribution guidelines](https://github.com/ItsMurumba/laravel-hostpinnacle/blob/main/Contribution.md)
