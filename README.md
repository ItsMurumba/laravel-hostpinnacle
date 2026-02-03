# Laravel Hostpinnacle

Official Laravel package for the **Hostpinnacle SMS API**. Quick SMS, Group SMS, and File Upload — with optional **SaaS multi-account** support.

## Quick start

```bash
composer require itsmurumba/laravel-hostpinnacle
```

```bash
php artisan hostpinnacle:install
```

Add to `.env`:

```env
HOSTPINNACLE_API_KEY=your-api-key
HOSTPINNACLE_SENDER_ID=your-sender-id
HOSTPINNACLE_LOGIN_USERNAME=your-username
HOSTPINNACLE_LOGIN_PASSWORD=your-password
HOSTPINNACLE_BASE_URL=https://smsportal.hostpinnacle.co.ke/SMSApi
```

Send SMS:

```php
use Itsmurumba\Hostpinnacle\Facades\Hostpinnacle;

$response = Hostpinnacle::sendQuickSMS([
    'mobile' => '254720xxxxxx',
    'msg' => 'Hello World!',
]);
```

## Testing the package locally (before publishing)

To try the package in a real Laravel app without publishing to Packagist:

**1. Path repository (recommended)** — In your Laravel app’s `composer.json` add a path repo and require the package:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/path/to/laravel-hostpinnacle"
        }
    ],
    "require": {
        "itsmurumba/laravel-hostpinnacle": "@dev"
    }
}
```

Then run `composer update`, add your Hostpinnacle env vars, and use the package as normal (e.g. `Hostpinnacle::sendQuickSMS(...)`). Changes in the package folder are used immediately (no re-publish).

**2. From a Git branch** — To test the install as users will (from GitHub), push your branch and in the app use:

```json
{
    "repositories": [{"type": "vcs", "url": "https://github.com/ItsMurumba/laravel-hostpinnacle"}],
    "require": {
        "itsmurumba/laravel-hostpinnacle": "dev-feature/saas-multi-account"
    }
}
```

Replace the branch name with yours. Run `composer update` in the app. Good for checking that `composer.json` and the repo are correct before a public release.

## Documentation

Full documentation (installation, configuration, usage, SaaS multi-account, testing, config reference) is built with [VitePress](https://vitepress.dev/) and lives in the **`docs/`** folder.

- **Local:** Run `npm install` then `npm run docs:dev` and open http://localhost:5174.
- **Build:** `npm run docs:build` — output in `docs/.vitepress/dist/` (suitable for GitHub Pages or any static host).

For GitHub Pages (project site), set `base: '/laravel-hostpinnacle/'` in `docs/.vitepress/config.mts` before building. For a custom domain, keep `base: '/'`.

## Contributing

Contributions are welcome. Please read [Contribution.md](Contribution.md) before submitting PRs or issues.

## License

MIT. See [LICENSE](LICENSE) for details.
