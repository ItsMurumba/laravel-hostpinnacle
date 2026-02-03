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

## Documentation

Full documentation (installation, configuration, usage, SaaS multi-account, testing, config reference) is built with [VitePress](https://vitepress.dev/) and lives in the **`docs/`** folder.

- **Local:** Run `npm install` then `npm run docs:dev` and open http://localhost:5173.
- **Build:** `npm run docs:build` — output in `docs/.vitepress/dist/` (suitable for GitHub Pages or any static host).

For GitHub Pages (project site), set `base: '/laravel-hostpinnacle/'` in `docs/.vitepress/config.mts` before building. For a custom domain, keep `base: '/'`.

## Contributing

Contributions are welcome. Please read [Contribution.md](Contribution.md) before submitting PRs or issues.

## License

MIT. See [LICENSE](LICENSE) for details.
