# Installation

Install the Laravel Hostpinnacle package via Composer:

```bash
composer require itsmurumba/laravel-hostpinnacle
```

## Laravel 5.5+

Package auto-discovery registers the service provider. Skip to [Configuration](/guide/configuration).

## Laravel 5.4 and below

Register the service provider and (optional) alias in `config/app.php`:

```php
'providers' => [
    // ...
    Itsmurumba\Hostpinnacle\HostpinnacleServiceProvider::class,
],
```

Next: [Configuration](/guide/configuration) to publish config and set env variables.
