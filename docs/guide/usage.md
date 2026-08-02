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

All `send*` methods return an `Illuminate\Http\Client\Response`; use `$response->successful()` or `$response->json()` as needed. So does every method below.

## Account administration

Beyond sending SMS, `Hostpinnacle` exposes one accessor per account-administration domain.
Each accessor returns a small client scoped to that domain; every method throws
`Itsmurumba\Hostpinnacle\Exceptions\IsNullException` if a required field is missing.

### 4. Schedule

Manage previously scheduled sends.

```php
// List scheduled messages in a date range
$response = $this->hostpinnacle->schedule()->read([
    'fromdate' => '2026-08-01',
    'todate' => '2026-08-31',
]);

// Reschedule a message
$response = $this->hostpinnacle->schedule()->update([
    'uuid' => '1234',
    'scheduletime' => '2026-08-13 00:09',
]);

// Cancel a scheduled message
$response = $this->hostpinnacle->schedule()->delete(['uuid' => '1234']);
```

### 5. Sender ID

Manage approved sender names.

```php
$response = $this->hostpinnacle->senderId()->create(['senderid' => 'MYBRAND']);
$response = $this->hostpinnacle->senderId()->read();
$response = $this->hostpinnacle->senderId()->update(['senderid' => 'NEWBRAND', 'id' => '69']);
$response = $this->hostpinnacle->senderId()->delete(['id' => '61,68']); // or ['senderid' => 'ABCDEF1,ABCDEF2']
```

### 6. Message Template

Manage reusable message bodies.

```php
$response = $this->hostpinnacle->messageTemplate()->create(['message' => 'Hello ###name###!']);
$response = $this->hostpinnacle->messageTemplate()->read();
$response = $this->hostpinnacle->messageTemplate()->update(['message' => 'Updated message', 'id' => '2']);
$response = $this->hostpinnacle->messageTemplate()->delete(['id' => '2,1']);
```

### 7. Draft

Manage saved title/content drafts.

```php
$response = $this->hostpinnacle->draft()->create([
    'title' => 'Launch announcement',
    'content' => 'We are live!',
]);
$response = $this->hostpinnacle->draft()->read();
$response = $this->hostpinnacle->draft()->update([
    'title' => 'Launch announcement (v2)',
    'content' => 'We are live! Updated.',
    'id' => '15',
]);
$response = $this->hostpinnacle->draft()->delete(['id' => '15,121']);
```

### 8. Webhook

Manage the endpoint that receives real-time delivery reports (DLR).

```php
$response = $this->hostpinnacle->webhook()->create([
    'smswebhook' => 'https://example.com/hostpinnacle/dlr',
    'smswebhookrate' => '10', // optional
]);
$response = $this->hostpinnacle->webhook()->read();
$response = $this->hostpinnacle->webhook()->update(['smswebhook' => 'https://example.com/new-dlr']);
$response = $this->hostpinnacle->webhook()->delete();
```

### 9. Account profile

Read or update the remote Hostpinnacle account's own status, profile, and credit history —
not to be confused with this package's SaaS `HostpinnacleAccount` model (see
[SaaS / Multi-Account](/guide/saas-multi-account)).

```php
$response = $this->hostpinnacle->accountProfile()->readStatus();
$response = $this->hostpinnacle->accountProfile()->readProfile();

// Only send the fields you want to change
$response = $this->hostpinnacle->accountProfile()->updateProfile([
    'fullname' => 'Jane Doe',
    'city' => 'Nairobi',
]);

$response = $this->hostpinnacle->accountProfile()->readCreditHistory([
    'fromdate' => '2026-08-01',
    'todate' => '2026-08-31',
]);
```

### 10. Password

```php
$response = $this->hostpinnacle->password()->change([
    'newpassword' => 'new-secret',
    'confirmpassword' => 'new-secret',
]);
```

### 11. API Key

```php
$response = $this->hostpinnacle->apiKeys()->create();
$response = $this->hostpinnacle->apiKeys()->read();
$response = $this->hostpinnacle->apiKeys()->update();
$response = $this->hostpinnacle->apiKeys()->delete();
```

### 12. Contact Group

Named groups of contacts — the same groups `sendGroupSMS()` sends to.

```php
$response = $this->hostpinnacle->contactGroups()->create(['groupname' => 'VIP Customers']);
$response = $this->hostpinnacle->contactGroups()->read();
$response = $this->hostpinnacle->contactGroups()->update(['groupname' => 'VIP Customers (Renamed)', 'id' => '10']);
$response = $this->hostpinnacle->contactGroups()->delete(['id' => '10']); // or ['groupname' => 'group1,group2']
```

### 13. Contact

Individual contacts, optionally assigned to a contact group.

```php
$response = $this->hostpinnacle->contacts()->create([
    'contactname' => 'Jane Doe',
    'mobileno' => '254720xxxxxx',
    'groupid' => '1', // optional
]);

// Same fields as create() — despite the name, the API has no file-upload variant here
$response = $this->hostpinnacle->contacts()->upload([
    'contactname' => 'Jane Doe',
    'mobileno' => '254720xxxxxx',
    'groupname' => 'VIP Customers', // optional
]);

$response = $this->hostpinnacle->contacts()->read();

$response = $this->hostpinnacle->contacts()->update([
    'id' => '7',
    'contactname' => 'Jane Doe',
    'mobileno' => '254720xxxxxx',
]);

$response = $this->hostpinnacle->contacts()->delete(['id' => '4,5,1']);
```
