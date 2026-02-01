<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Itsmurumba\Hostpinnacle\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Create mock file object for Hostpinnacle file-upload SMS tests.
 * Optionally pass temp file content; returns object with getClientOriginalExtension() and __toString() (path).
 */
function hostpinnacle_mock_file(string $content = "Phone\n254700000000\n", string $extension = 'csv'): object
{
    $tempFile = tmpfile();
    $path = stream_get_meta_data($tempFile)['uri'];
    fwrite($tempFile, $content);

    return new class($path, $tempFile, $extension) {
        private $path;
        private $handle;
        private $extension;

        public function __construct($path, $handle, string $extension)
        {
            $this->path = $path;
            $this->handle = $handle;
            $this->extension = $extension;
        }

        public function getClientOriginalExtension(): string
        {
            return $this->extension;
        }

        public function __toString(): string
        {
            return $this->path;
        }

        public function close(): void
        {
            if (is_resource($this->handle)) {
                fclose($this->handle);
            }
        }
    };
}

/**
 * Set Hostpinnacle config for tests.
 */
function hostpinnacle_set_config(): void
{
    \Illuminate\Support\Facades\Config::set('hostpinnacle.baseUrl', 'https://api.hostpinnacle.test');
    \Illuminate\Support\Facades\Config::set('hostpinnacle.apiKey', 'test-api-key');
    \Illuminate\Support\Facades\Config::set('hostpinnacle.senderId', 'TESTID');
    \Illuminate\Support\Facades\Config::set('hostpinnacle.username', 'testuser');
    \Illuminate\Support\Facades\Config::set('hostpinnacle.password', 'testpass');
}
