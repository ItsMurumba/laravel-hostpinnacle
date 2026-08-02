<?php

namespace Itsmurumba\Hostpinnacle;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Hostpinnacle SMS API client. Sends Quick SMS, Group SMS, and File Upload SMS.
 * Uses config (env) when constructed with null; use HostpinnacleCredentials for per-account credentials.
 */
class Hostpinnacle
{
    /** @var string API key from Hostpinnacle Dashboard */
    protected $apiKey;

    /** @var mixed Last response from the API */
    protected $response;

    /** @var string Hostpinnacle API base URL */
    protected $baseUrl;

    /** @var string Approved Sender ID from Hostpinnacle Dashboard */
    protected $senderId;

    /** @var string Username for Hostpinnacle Portal */
    protected $username;

    /** @var string Password for Hostpinnacle Portal */
    protected $password;

    /** @var \Itsmurumba\Hostpinnacle\HostpinnacleCredentials Resolved credentials, reused by the Api\* accessors */
    protected $credentials;

    /**
     * Create a new Hostpinnacle instance. Uses config when credentials are null.
     *
     * @param  \Itsmurumba\Hostpinnacle\HostpinnacleCredentials|null  $credentials
     */
    public function __construct(?HostpinnacleCredentials $credentials = null)
    {
        $creds = $credentials ?? HostpinnacleCredentials::fromConfig();

        $this->apiKey = $creds->getApiKey();
        $this->senderId = $creds->getSenderId();
        $this->username = $creds->getUsername();
        $this->password = $creds->getPassword();
        $this->baseUrl = $creds->getBaseUrl() ?? Config::get('hostpinnacle.baseUrl');

        $this->credentials = new HostpinnacleCredentials(
            $this->apiKey,
            $this->senderId,
            $this->username,
            $this->password,
            $this->baseUrl
        );
    }

    /**
     * Access the Schedule API (list/reschedule/cancel scheduled SMS).
     *
     * @return \Itsmurumba\Hostpinnacle\Api\ScheduleClient
     */
    public function schedule(): Api\ScheduleClient
    {
        return new Api\ScheduleClient($this->credentials);
    }

    /**
     * Access the Sender ID API (create/read/update/delete approved sender names).
     *
     * @return \Itsmurumba\Hostpinnacle\Api\SenderIdClient
     */
    public function senderId(): Api\SenderIdClient
    {
        return new Api\SenderIdClient($this->credentials);
    }

    /**
     * Access the Message Template API (create/read/update/delete reusable message bodies).
     *
     * @return \Itsmurumba\Hostpinnacle\Api\MessageTemplateClient
     */
    public function messageTemplate(): Api\MessageTemplateClient
    {
        return new Api\MessageTemplateClient($this->credentials);
    }

    /**
     * Access the Draft API (create/read/update/delete saved title/content drafts).
     *
     * @return \Itsmurumba\Hostpinnacle\Api\DraftClient
     */
    public function draft(): Api\DraftClient
    {
        return new Api\DraftClient($this->credentials);
    }

    /**
     * Build the payload array for Send SMS Batch, Group, or File requests.
     *
     * @param  string|null  $contacts
     * @param  string|null  $groupIds
     * @param  mixed  $file
     * @param  string|null  $scheduled
     * @param  string|null  $trackLink  Enable link tracking (smartlink)
     * @param  string|null  $smartLinkTitle  Title to identify the tracked link
     * @return array<string, mixed>
     */
    private function formattedSmsData(string $sendMethod, ?string $message, string $messageType, $contacts = null, $groupIds = null, $file = null, $scheduled = null, $trackLink = null, $smartLinkTitle = null): array
    {
        $data = array_filter([
            "userid" => $this->username,
            "password" => $this->password,
            "msg" => $message,
            "sendMethod" => $sendMethod,
            "senderid" => $this->senderId,
            "msgType" => $messageType,
            "duplicatecheck" => "true",
            "output" => "json"
        ]);

        if ($groupIds != null) {
            $data["group"] = $groupIds;
        } elseif ($contacts != null) {
            $data["mobile"] = $contacts;
        } elseif ($file != null) {
            $data["file"] = $file;
        }

        if ($scheduled != null) {
            $data["scheduleTime"] = $scheduled;
        }

        if ($trackLink != null) {
            $data["trackLink"] = $trackLink;
        }

        if ($smartLinkTitle != null) {
            $data["smartLinkTitle"] = $smartLinkTitle;
        }

        return $data;
    }

    /**
     * Send quick SMS in batches (single or comma-separated mobiles). Country code required for international.
     * Pass trackLink/smartLinkTitle to enable link tracking (smartlink) on links in the message.
     *
     * @param  array{msg: string, mobile: string, trackLink?: string, smartLinkTitle?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendQuickSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['mobile'])) {
            throw new IsNullException('msg and mobile must not be null');
        }

        $payload = $this->formattedSmsData(
            'quick',
            $data['msg'],
            'text',
            $data['mobile'],
            null,
            null,
            null,
            $data['trackLink'] ?? null,
            $data['smartLinkTitle'] ?? null
        );

        return Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->post(
            $this->baseUrl . '/send',
            $payload
        );
    }

    /**
     * Send quick SMS at a scheduled date/time.
     *
     * @param  array{msg: string, mobile: string, scheduledTime: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendQuickScheduledSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['mobile'])) {
            throw new IsNullException('msg and mobile must not be null');
        }

        $payload = $this->formattedSmsData('quick', $data['msg'], 'text', $data['mobile'], null, null, $data['scheduledTime']);

        return Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->post(
            $this->baseUrl . '/send',
            $payload
        );
    }

    /**
     * Send SMS to one or more groups (single or comma-separated group IDs). Country code required for international.
     * Pass trackLink/smartLinkTitle to enable link tracking (smartlink) on links in the message.
     *
     * @param  array{msg: string, groupIds: string, trackLink?: string, smartLinkTitle?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendGroupSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['groupIds'])) {
            throw new IsNullException('msg and groupIds must not be null');
        }

        $payload = $this->formattedSmsData(
            'group',
            $data['msg'],
            'text',
            null,
            $data['groupIds'],
            null,
            null,
            $data['trackLink'] ?? null,
            $data['smartLinkTitle'] ?? null
        );

        return Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->post(
            $this->baseUrl . '/send',
            $payload
        );
    }

    /**
     * Send group SMS at a scheduled date/time.
     *
     * @param  array{msg: string, groupIds: string, scheduledTime: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendGroupScheduledSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['groupIds'])) {
            throw new IsNullException('msg and groupIds must not be null');
        }

        $payload = $this->formattedSmsData('group', $data['msg'], 'text', null, $data['groupIds'], null, $data['scheduledTime']);

        return Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->post(
            $this->baseUrl . '/send',
            $payload
        );
    }

    /**
     * Send SMS from file with mobile numbers only (first row header: Phone). Country code required e.g. 254720000000.
     * Pass trackLink/smartLinkTitle to enable link tracking (smartlink) on links in the message.
     *
     * @param  array{msg: string, file: \Illuminate\Http\UploadedFile|object, trackLink?: string, smartLinkTitle?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendMobileOnlyFileSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['file'])) {
            throw new IsNullException('msg and file must not be null');
        }

        $payload = $this->formattedSmsData(
            'bulkupload',
            $data['msg'],
            'text',
            null,
            null,
            null,
            null,
            $data['trackLink'] ?? null,
            $data['smartLinkTitle'] ?? null
        );

        $extension = $data['file']->getClientOriginalExtension();

        return Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->attach(
            'file',
            file_get_contents($data['file']),
            'file.' . $extension
        )->post(
            $this->baseUrl . '/send',
            $payload
        );
    }

    /**
     * Send SMS from file (mobile numbers only) at a scheduled date/time.
     *
     * @param  array{msg: string, file: \Illuminate\Http\UploadedFile|object, scheduledTime: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendMobileOnlyFileScheduledSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['file'])) {
            throw new IsNullException('msg and file must not be null');
        }

        $payload = $this->formattedSmsData('bulkupload', $data['msg'], 'text', null, null, null, $data['scheduledTime']);

        $extension = $data['file']->getClientOriginalExtension();

        return Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->attach(
            'file',
            file_get_contents($data['file']),
            'file.' . $extension
        )->post(
            $this->baseUrl . '/send',
            $payload
        );
    }

    /**
     * Send SMS from file with mobile numbers and message (first row header: Phone, Message). Country code required.
     *
     * @param  array{file: \Illuminate\Http\UploadedFile|object}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendMobileAndMessageFileSMS($data)
    {
        if (!isset($data['file'])) {
            throw new IsNullException('file must not be null');
        }

        $payload = $this->formattedSmsData('bulkupload', null, 'text');

        $extension = $data['file']->getClientOriginalExtension();

        return Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->attach(
            'file',
            file_get_contents($data['file']),
            'file.' . $extension
        )->post(
            $this->baseUrl . '/send',
            $payload
        );
    }

    /**
     * Send SMS from file (mobile + message) at a scheduled date/time.
     *
     * @param  array{file: \Illuminate\Http\UploadedFile|object, scheduledTime: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendMobileAndMessageFileScheduledSMS($data)
    {
        if (!isset($data['file'])) {
            throw new IsNullException('file must not be null');
        }

        $payload = $this->formattedSmsData('bulkupload', null, 'text', null, null, null, $data['scheduledTime']);

        $extension = $data['file']->getClientOriginalExtension();

        return Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->attach(
            'file',
            file_get_contents($data['file']),
            'file.' . $extension
        )->post(
            $this->baseUrl . '/send',
            $payload
        );
    }
}
