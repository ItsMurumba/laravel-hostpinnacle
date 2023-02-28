<?php

namespace Itsmurumba\Hostpinnacle;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

class Hostpinnacle
{
    /**
     * Issue API Key from your Hostpinnacle Dashboard
     * @var string
     */
    protected $apiKey;

    /**
     * Instance of Client
     * @var Client
     */
    protected $client;

    /**
     * Response from requests made to Hostpinnacle
     * @var Mixed
     */
    protected $response;

    /**
     * Hostpinnacle API base URL
     * @var string
     */
    protected $baseUrl;

    /**
     * Approved SenderId from Hostpinnacle Dashboard
     */
    protected $senderId;

    /**
     * Username for logging into Hostpinnacle Portal
     */
    protected $username;

    /**
     * Password for logging into Hostpinnacle Portal
     */
    protected $password;

    public function __construct()
    {

        $this->setApiKey();
        $this->setBaseUrl();
        $this->setRequestOptions();
        $this->setSenderId();
        $this->setUsername();
        $this->setPassword();
    }

    /**
     * Get Base URL from Hostpinnacle config file
     */
    public function setBaseUrl()
    {
        $this->baseUrl = Config::get('hostpinnacle.baseUrl');
    }

    /**
     * Get API Key from Hostpinnacle config file
     */
    public function setApiKey()
    {
        $this->apiKey = Config::get('hostpinnacle.apiKey');
    }

    /**
     * Get Sender Id from Hostpinnacle config file
     */
    public function setSenderId()
    {
        $this->senderId = Config::get('hostpinnacle.senderId');
    }

    /**
     * Get Username from Hostpinnacle config file
     */
    public function setUsername()
    {
        $this->username = Config::get('hostpinnacle.username');
    }

    /**
     * Get Password from Hostpinnacle config file
     */
    public function setPassword()
    {
        $this->password = Config::get('hostpinnacle.password');
    }

    /**
     * Set options for making the Client request
     */
    private function setRequestOptions()
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
     * Undocumented function
     *
     * @param [type] $relativeUrl
     * @param [type] $method
     * @param array $body
     * @return void
     */
    private function setHttpResponse($relativeUrl, $method, $body = [])
    {

        if (is_null($method)) {
            throw new IsNullException('Method must not be null');
        }

        $response = $this->client->{strtolower($method)}(
            $this->baseUrl . $relativeUrl,
            ["body" => json_encode($body)]
        );

        return $response;
    }

    /**
     * Formatted SMS Data
     * Takes varaible and return an array of data to be used in Send SMS Batch, Send SMS Group and Send SM File requests
     *
     * @param [type] $sendMethod
     * @param [type] $message
     * @param [type] $messageType
     * @param [type] $contacts
     * @param [type] $groupIds
     * @return void
     */
    private function formattedSmsData($sendMethod, $message, $messageType, $contacts = null,  $groupIds = null, $file = null, $scheduled = null)
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
     * Send Quick SMS
     * Send SMS in batches. You can send single SMS or comma separated mobile numbers.
     * Country code is must for international messaging.
     *
     * @param [type] $data
     * @return void
     */
    public function sendQuickSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['mobile'])) {
            throw new IsNullException('msg and mobile must not be null');
        }

        $payload = $this->formattedSmsData('quick', $data['msg'], 'text', $data['mobile']);

        $response = Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->post(
            $this->baseUrl . '/send',
            $payload
        );


        return $response;
    }

    /**
     * Send Quick Scheduled SMS.
     *
     * @param [type] $data
     * @return void
     */
    public function sendQuickScheduledSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['mobile'])) {
            throw new IsNullException('msg and mobile must not be null');
        }

        $payload = $this->formattedSmsData('quick', $data['msg'], 'text', $data['mobile'], null, null, $data['scheduledTime']);

        $response = Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->post(
            $this->baseUrl . '/send',
            $payload
        );


        return $response;
    }

    /**
     * Send SMS Group
     * Send SMS to your groups. You can send to single group or comma separated groups.
     * Country code is must for international messaging.
     *
     * @param [type] $data
     * @return void
     */
    public function sendGroupSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['groupIds'])) {
            throw new IsNullException('msg and groupIds must not be null');
        }

        $payload = $this->formattedSmsData('group', $data['msg'], 'text', null, $data['groupIds']);

        $response = Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->get(
            $this->baseUrl . '/send',
            $payload
        );

        return $response;
    }

    /**
     * Send SMS Group Scheduled
     * Send SMS to your groups. You can send to single group or comma separated groups.
     * Country code is must for international messaging.
     *
     * @param [type] $data
     * @return void
     */
    public function sendGroupScheduledSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['groupIds'])) {
            throw new IsNullException('msg and groupIds must not be null');
        }

        $payload = $this->formattedSmsData('group', $data['msg'], 'text', null, $data['groupIds'], null, $data['scheduledTime']);

        $response = Http::asForm()->withHeaders([
            'apikey' => $this->apiKey,
            'cache-control' => 'no-cache'
        ])->get(
            $this->baseUrl . '/send',
            $payload
        );

        return $response;
    }

    /**
     * Send Mobile Only File SMS
     * Send SMS using File Upload. You can upload only mobile in a file with the first row being the header (Phone)
     * Country code is must for international messaging eg 254720000000
     */
    public function sendMobileOnlyFileSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['file'])) {
            throw new IsNullException('msg and file must not be null');
        }

        $payload = $this->formattedSmsData('bulkupload', $data['msg'], 'text');

        $extension = $data['file']->getClientOriginalExtension();

        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->attach(
            'file',
            file_get_contents($data['file']),
            'file.' . $extension
        )->post(
            $this->baseUrl . '/send',
            $payload
        );

        return $response;
    }

    /**
     * Send Mobile Only File Scheduled SMS
     * Send SMS later on a scheduled data/time using File Upload. You can upload only mobile in a file with the first row being the header (Phone)
     * Country code is must for international messaging eg 254720000000
     */
    public function sendMobileOnlyFileScheduledSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['file'])) {
            throw new IsNullException('msg and file must not be null');
        }

        $payload = $this->formattedSmsData('bulkupload', $data['msg'], 'text', null, null, null, $data['scheduledTime']);

        $extension = $data['file']->getClientOriginalExtension();

        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->attach(
            'file',
            file_get_contents($data['file']),
            'file.' . $extension
        )->post(
            $this->baseUrl . '/send',
            $payload
        );

        return $response;
    }

    /**
     * Send Mobile and Message File SMS
     * Send SMS using File Upload. You can upload only mobile in a file with the first row being the header (Phone, Message)
     * Country code is must for international messaging eg 254720000000
     */
    public function sendMobileAndMessageFileSMS($data)
    {
        if (!isset($data['file'])) {
            throw new IsNullException('file must not be null');
        }

        $payload = $this->formattedSmsData('bulkupload', null, 'text');

        $extension = $data['file']->getClientOriginalExtension();

        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->attach(
            'file',
            file_get_contents($data['file']),
            'file.' . $extension
        )->post(
            $this->baseUrl . '/send',
            $payload
        );

        return $response;
    }

    /**
     * Send Mobile and Message File Scheduled SMS
     * Send SMS later on a scheduled data/time using File Upload. You can upload only mobile in a file with the first row being the header (Phone)
     * Country code is must for international messaging eg 254720000000
     */
    public function sendMobileAndMessageFileScheduledSMS($data)
    {
        if (!isset($data['msg']) || !isset($data['file'])) {
            throw new IsNullException('msg and file must not be null');
        }

        $payload = $this->formattedSmsData('bulkupload', $data['msg'], 'text', null, null, null, $data['scheduledTime']);

        $extension = $data['file']->getClientOriginalExtension();

        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->attach(
            'file',
            file_get_contents($data['file']),
            'file.' . $extension
        )->post(
            $this->baseUrl . '/send',
            $payload
        );

        return $response;
    }
}
