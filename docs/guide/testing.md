# Testing

Tests use **[Pest](https://pestphp.com/)** (PHP testing framework) and **[Orchestra Testbench](https://orchestraplatform.com/docs/testbench)** for Laravel package testing.

## Run tests

```bash
composer test
```

Or directly with Pest:

```bash
vendor/bin/pest
```

## Code coverage

Code coverage (optional) requires a coverage driver: **PCOV** or **Xdebug**. Without one, `composer test:coverage` will report "No code coverage driver is available".

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
