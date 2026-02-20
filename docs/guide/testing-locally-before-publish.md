# Testing the package locally (before publishing)

You can use the package in a real Laravel application without publishing it to Packagist. Two common approaches:

## 1. Path repository (recommended)

Use the package from your filesystem so you can edit the package code and see changes in the app immediately.

1. **Create or use an existing Laravel app** (e.g. `my-laravel-app`).

2. **Add a path repository** in the app’s `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../laravel-hostpinnacle"
        }
    ],
    "require": {
        "itsmurumba/laravel-hostpinnacle": "@dev"
    }
}
```

Adjust `url` to the actual path to your package (absolute or relative to the app).

3. **Install/update** from the app directory:

```bash
cd my-laravel-app
composer update itsmurumba/laravel-hostpinnacle
```

4. **Configure and use** the package as usual:

- Add Hostpinnacle env vars to `.env`
- Run `php artisan hostpinnacle:install` if you want to publish config
- Use the facade or class in controllers, jobs, etc.

Composer will symlink the package, so changes in `laravel-hostpinnacle` are reflected in the app without running `composer update` again.

## 2. From a Git branch (VCS)

Test the package as it would be installed from GitHub (e.g. to verify `composer.json` and the repository before a public release).

1. **Push your branch** to GitHub (e.g. `feature/saas-multi-account` or `main`).

2. **In your Laravel app’s `composer.json`** add the VCS repo and require the branch:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ItsMurumba/laravel-hostpinnacle"
        }
    ],
    "require": {
        "itsmurumba/laravel-hostpinnacle": "dev-feature/saas-multi-account"
    }
}
```

Replace `dev-feature/saas-multi-account` with your branch name (Composer uses `dev-<branch>` for non-stable branches).

3. **Install/update** from the app directory:

```bash
composer update itsmurumba/laravel-hostpinnacle
```

4. **Configure and test** the app. To pick up package changes, push to the branch and run `composer update itsmurumba/laravel-hostpinnacle` again.

## What to test

- **Single-account:** Set env vars, send Quick SMS, Group SMS, or File Upload via the facade or `new Hostpinnacle()`.
- **SaaS:** Set `HOSTPINNACLE_SAAS_ENABLED=true`, run migrations, register/auth a user, create an account via API or web routes, then `Hostpinnacle::for($account)->sendQuickSMS(...)`.
- **Artisan:** `php artisan hostpinnacle:install` and overwrite behaviour when config exists.

Once everything works, you can tag a release and publish to Packagist.
