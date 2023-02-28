## laravel-sdk
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


# Contribution
This is a community package and thus welcome anyone intrested to contribute in improving the package. Kindly go through the [Contribution.md](Contribution.md) before starting to contribute. Keep those PRs and Issues coming.

# Buy Me Coffee
Give this repo a star and i will have my super powers recharged. You can also follow me on twitter [@ItsMurumba](https://twitter.com/ItsMurumba)

# License
This package is licensed under the MIT License. Please review the [License](LICENSE.md) file for details
