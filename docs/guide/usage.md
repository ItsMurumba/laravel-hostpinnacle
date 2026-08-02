# Usage

Single-account usage: one set of credentials from config/env, used app-wide.

## Using the class

Inject or instantiate the `Hostpinnacle` class:

```php
protected $hostpinnacle;

public function __construct()
{
    $this->hostpinnacle = new Hostpinnacle();
}
```

## Using the facade

```php
use Itsmurumba\Hostpinnacle\Facades\Hostpinnacle;

$response = Hostpinnacle::sendQuickSMS(['mobile' => '254720xxxxxx', 'msg' => 'Hello World!']);
```

## 1. Quick SMS

**Send quick SMS (batch):**

```php
$data['mobile'] = '254720xxxxxx';
$data['msg'] = 'Hello World!';
$response = $this->hostpinnacle->sendQuickSMS($data);
```

**Send quick SMS with link tracking (smartlink):**

`sendQuickSMS`, `sendGroupSMS`, and `sendMobileOnlyFileSMS` accept optional `trackLink` and
`smartLinkTitle` keys to enable link tracking on any link in the message:

```php
$data['mobile'] = '254720xxxxxx';
$data['msg'] = 'Check out https://example.com';
$data['trackLink'] = 'true';
$data['smartLinkTitle'] = 'My Example.com Short Link';
$response = $this->hostpinnacle->sendQuickSMS($data);
```

**Send quick scheduled SMS:**

```php
$data['scheduledTime'] = '2023-02-28 17:32:03';
$data['mobile'] = '254720xxxxxx';
$data['msg'] = 'Hello World!';
$response = $this->hostpinnacle->sendQuickScheduledSMS($data);
```

## 2. Group SMS

**Send group SMS:**

```php
$data['groupIds'] = '1056';
$data['msg'] = 'Hello World!';
$response = $this->hostpinnacle->sendGroupSMS($data);
```

**Send group scheduled SMS:**

```php
$data['scheduledTime'] = '2023-02-28 17:32:03';
$data['groupIds'] = '1056';
$data['msg'] = 'Hello World!';
$response = $this->hostpinnacle->sendGroupScheduledSMS($data);
```

## 3. File Upload SMS

**Mobile numbers only (message in code):**

```php
$data['file'] = $request->file('file');
$data['msg'] = 'Hello World!';
$response = $this->hostpinnacle->sendMobileOnlyFileSMS($data);
```

**Mobile numbers only, scheduled:**

```php
$data['scheduledTime'] = '2023-02-28 17:32:03';
$data['file'] = $request->file('file');
$data['msg'] = 'Hello World!';
$response = $this->hostpinnacle->sendMobileOnlyFileScheduledSMS($data);
```

**File with mobile numbers and message in file:**

```php
$data['file'] = $request->file('file');
$response = $this->hostpinnacle->sendMobileAndMessageFileSMS($data);
```

**File with mobile and message, scheduled:**

```php
$data['scheduledTime'] = '2023-02-28 17:32:03';
$data['file'] = $request->file('file');
$response = $this->hostpinnacle->sendMobileAndMessageFileScheduledSMS($data);
```

All methods return an `Illuminate\Http\Client\Response`; use `$response->successful()` or `$response->json()` as needed.
