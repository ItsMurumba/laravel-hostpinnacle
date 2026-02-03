# Configuration

## Publish config

After installing, publish the `hostpinnacle.php` config file:

```bash
php artisan hostpinnacle:install
```

Or via vendor publish:

```bash
php artisan vendor:publish
```

Then select the Hostpinnacle config (or use `--tag=hostpinnacle-config`).

## Environment variables

Add and define the following in your `.env` file:

```env
HOSTPINNACLE_API_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxx
HOSTPINNACLE_SENDER_ID=xxxxxxxxxx
HOSTPINNACLE_LOGIN_USERNAME=xxxxxxxxxxxxxxx
HOSTPINNACLE_LOGIN_PASSWORD=xxxxxx
HOSTPINNACLE_BASE_URL=https://smsportal.hostpinnacle.co.ke/SMSApi
```

These are used for **single-account** mode (one set of credentials app-wide). For **SaaS multi-account**, see [SaaS / Multi-Account](/guide/saas-multi-account).

For full config options (including SaaS), see [Config reference](/reference/config).
