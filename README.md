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
