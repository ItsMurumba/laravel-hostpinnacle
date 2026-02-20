<?php

namespace Itsmurumba\Hostpinnacle;

use GuzzleHttp\Client;
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

    /** @var Client Guzzle HTTP client */
    protected $client;

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

        $this->setRequestOptions();
    }

    /**
     * Configure the Guzzle client with base URI and headers.
     */
    private function setRequestOptions(): void
    {
        $this->client = new Client(
            [
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'apikey' => $this->apiKey,
                    'content-type'  => 'application/x-www-form-urlencoded',
                    'cache-control' => 'no-cache'
                ]
            ]
        );
    }

    /**
     * Send an HTTP request and return the response.
     *
     * @param  string  $relativeUrl
     * @param  string  $method
     * @param  array<string, mixed>  $body
     * @return \Psr\Http\Message\ResponseInterface
     * @throws IsNullException
     */
    private function setHttpResponse($relativeUrl, $method, $body = [])
    {
        if (is_null($method)) {
            throw new IsNullException('Method must not be null');
        }

        return $this->client->{strtolower($method)}(
            $this->baseUrl . $relativeUrl,
            ["body" => json_encode($body)]
        );
    }

    /**
     * Build the payload array for Send SMS Batch, Group, or File requests.
     *
     * @param  string|null  $contacts
     * @param  string|null  $groupIds
     * @param  mixed  $file
     * @param  string|null  $scheduled
     * @return array<string, mixed>
     */
    private function formattedSmsData(string $sendMethod, ?string $message, string $messageType, $contacts = null, $groupIds = null, $file = null, $scheduled = null): array
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

        return $data;
    }

    /**
     * Send quick SMS in batches (single or comma-separated mobiles). Country code required for international.
     *
     * @param  array{msg: string, mobile: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendQuickSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['mobile'])) {
            throw new IsNullException('msg and mobile must not be null');
        }

        $payload = $this->formattedSmsData('quick', $data['msg'], 'text', $data['mobile']);

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
     *
     * @param  array{msg: string, groupIds: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendGroupSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['groupIds'])) {
            throw new IsNullException('msg and groupIds must not be null');
        }

        $payload = $this->formattedSmsData('group', $data['msg'], 'text', null, $data['groupIds']);

        return Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->get(
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
        ])->get(
            $this->baseUrl . '/send',
            $payload
        );
    }

    /**
     * Send SMS from file with mobile numbers only (first row header: Phone). Country code required e.g. 254720000000.
     *
     * @param  array{msg: string, file: \Illuminate\Http\UploadedFile|object}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function sendMobileOnlyFileSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['file'])) {
            throw new IsNullException('msg and file must not be null');
        }

        $payload = $this->formattedSmsData('bulkupload', $data['msg'], 'text');

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
