# API Coverage Roadmap

The package currently wraps a subset of the Hostpinnacle SMS API — sending only. The
[official Postman collection](https://smsportal.hostpinnacle.co.ke) documents ~40 more
endpoints across 9 resource domains (schedule management, sender IDs, templates, drafts,
webhooks, account/profile, password, API keys, contact groups, contacts) that this package
doesn't touch yet. This doc tracks what's implemented, the architecture for the rest, and
the order it'll ship in.

## Current state

| Domain | Endpoint | Status |
|---|---|---|
| Quick SMS | Send | ✅ `Hostpinnacle::sendQuickSMS()` |
| | Send (scheduled) | ✅ `Hostpinnacle::sendQuickScheduledSMS()` |
| | Send with link tracking (smartlink) | 🔜 Phase 0 — optional `trackLink`/`smartLinkTitle` keys on `sendQuickSMS()` |
| Group SMS | Send | ⚠️ `Hostpinnacle::sendGroupSMS()` — sends as **GET**; the API documents this as **POST** with a form body. Fixing in Phase 0. |
| | Send (scheduled) | ⚠️ `Hostpinnacle::sendGroupScheduledSMS()` — same GET/POST bug |
| | Send with link tracking (smartlink) | 🔜 Phase 0 — optional keys on `sendGroupSMS()` |
| File upload (mobile only) | Send | ✅ `Hostpinnacle::sendMobileOnlyFileSMS()` |
| | Send (scheduled) | ✅ `Hostpinnacle::sendMobileOnlyFileScheduledSMS()` |
| | Send with link tracking (smartlink) | 🔜 Phase 0 — optional keys on `sendMobileOnlyFileSMS()` |
| File upload (mobile + message) | Send | ✅ `Hostpinnacle::sendMobileAndMessageFileSMS()` |
| | Send (scheduled) | ✅ `Hostpinnacle::sendMobileAndMessageFileScheduledSMS()` |
| Schedule | Read / Update / Delete | ✅ `Hostpinnacle::schedule()->read()/update()/delete()` |
| Sender ID | Create / Read / Update / Delete | ✅ `Hostpinnacle::senderId()->create()/read()/update()/delete()` |
| Message Template | Create / Read / Update / Delete | ✅ `Hostpinnacle::messageTemplate()->create()/read()/update()/delete()` |
| Draft | Create / Read / Update / Delete | ✅ `Hostpinnacle::draft()->create()/read()/update()/delete()` |
| Webhook | Create / Read / Update / Delete | 🔜 Phase 3 |
| Account profile | Read status / Read profile / Update profile / Read credit history | 🔜 Phase 3 |
| Password | Change | 🔜 Phase 3 |
| API Key | Create / Read / Update / Delete | 🔜 Phase 3 |
| Contact Group | Create / Read / Update / Delete | 🔜 Phase 4 |
| Contact | Create / Upload / Read / Update / Delete | 🔜 Phase 4 |

**Out of scope for now:** the Postman *environment* references an `OTP_SERVER`, but the
collection has no OTP requests in it. Nothing to implement against until there's a spec.

## Architecture

The existing `Hostpinnacle` class stays the entry point for **sending** SMS — that's its
one job and it's well-tested (`src/Hostpinnacle.php`). The 9 new domains above are a
different axis (account administration, not sending), and at ~40 methods a flat class would
be an unreadable God object. So:

```
Hostpinnacle                          (unchanged: sendQuickSMS, sendGroupSMS, etc.)
  ->schedule(): Api\ScheduleClient
  ->senderId(): Api\SenderIdClient
  ->messageTemplate(): Api\MessageTemplateClient
  ->draft(): Api\DraftClient
  ->webhook(): Api\WebhookClient
  ->accountProfile(): Api\AccountProfileClient   // not Api\AccountClient — avoid confusion
  ->password(): Api\PasswordClient               // with Models\HostpinnacleAccount (SaaS storage)
  ->apiKeys(): Api\ApiKeyClient
  ->contactGroups(): Api\ContactGroupClient      // not Api\GroupClient — avoid confusion
  ->contacts(): Api\ContactClient                // with sendGroupSMS's "group" (SMS recipients)

Api\BaseApiClient (abstract)          shared request boilerplate: Http::asForm(),
  ├─ ScheduleClient                   apikey header, userid/password/output=json defaults
  ├─ SenderIdClient                   (same pattern as Hostpinnacle::formattedSmsData(),
  ├─ MessageTemplateClient            just factored out since 10 classes need it instead of 1)
  ├─ DraftClient
  ├─ WebhookClient
  ├─ AccountProfileClient
  ├─ PasswordClient
  ├─ ApiKeyClient
  ├─ ContactGroupClient
  └─ ContactClient
```

Each accessor builds its client from the same `HostpinnacleCredentials` already resolved on
the parent, so SaaS multi-account works with no extra plumbing:
`Hostpinnacle::for($account)->schedule()->read(...)`.

**Naming notes** (both are real collision risks in this codebase, not hypothetical):
- `Models\HostpinnacleAccount` already means "a SaaS tenant's stored credentials." The new
  account-status/profile/credit-history domain is a different concept (the remote
  Hostpinnacle account itself) — hence `AccountProfileClient`, not `AccountClient`.
- `Http\Resources\HostpinnacleAccountResource` already means "Laravel API Resource / JSON
  transformer." The new domain classes are API *clients*, not response transformers —
  hence the `*Client` suffix under `Api\`, not `*Resource`.
- `sendGroupSMS($data['groupIds'])` already means "send to a contact group." The new Group
  CRUD domain manages those same contact groups — hence `contactGroups()` /
  `ContactGroupClient`, so it reads distinctly from the sending verb.

## Conventions (every new endpoint follows these — no exceptions per-domain)

- Return the raw `Illuminate\Http\Client\Response` from every method, same as the existing
  `send*` methods. No new response DTO/wrapper layer.
- Required fields missing → throw `IsNullException` (now `extends InvalidArgumentException`),
  same as `sendQuickSMS` etc. already do.
- All requests go through `Illuminate\Support\Facades\Http` — never Guzzle directly.
- One test file per client under `tests/Unit/Api/`, using `Http::fake()` +
  `Http::assertSent()` — the same pattern as `tests/Unit/Hostpinnacle/SendQuickSMSTest.php`.
- All domains share the single `baseUrl`/`apiKey` already in `HostpinnacleCredentials` — the
  Postman environment confirms every endpoint in scope uses the same `{{SERVER}}`. No new
  config keys needed.

## Phased delivery

Each phase is sized to ship as one PR.

- **Phase 0 — fix + smartlink.** Fix `sendGroupSMS`/`sendGroupScheduledSMS` to POST instead
  of GET. Add optional `trackLink`/`smartLinkTitle` keys to `sendQuickSMS`, `sendGroupSMS`,
  `sendMobileOnlyFileSMS` (no new methods — the collection has no smartlink+scheduled
  variant, so scheduled sends are untouched). No `Api\BaseApiClient` here — it'd have no
  caller yet, which is just scaffolding-for-later.
- **Phase 1 — Schedule.** Introduce `Api\BaseApiClient` and `ScheduleClient::read()/update()/delete()`
  together — `ScheduleClient` is `BaseApiClient`'s first real consumer, validating the
  pattern before the bigger phases reuse it.
- **Phase 2 — setup resources.** `SenderIdClient`, `MessageTemplateClient`, `DraftClient`
  (12 endpoints) — things you configure before/while sending.
- **Phase 3 — account administration.** `WebhookClient`, `AccountProfileClient`,
  `PasswordClient`, `ApiKeyClient` (13 endpoints).
- **Phase 4 — audience management.** `ContactGroupClient`, `ContactClient` (9 endpoints) —
  pairs naturally with the existing `sendGroupSMS`.

Update this table's 🔜/✅ status as each phase merges.
