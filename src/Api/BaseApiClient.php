<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Itsmurumba\Hostpinnacle\HostpinnacleCredentials;

/**
 * Shared request boilerplate for the account-administration API clients
 * (Schedule, SenderId, Template, Draft, Webhook, Account, Password, ApiKey,
 * ContactGroup, Contact). Mirrors Hostpinnacle::formattedSmsData()'s
 * userid/password/output=json + apikey header pattern.
 */
abstract class BaseApiClient
{
    protected HostpinnacleCredentials $credentials;

    protected string $baseUrl;

    public function __construct(HostpinnacleCredentials $credentials)
    {
        $this->credentials = $credentials;
        $this->baseUrl = $credentials->getBaseUrl() ?? Config::get('hostpinnacle.baseUrl');
    }

    /**
     * POST to $endpoint with the shared userid/password/output=json fields merged in.
     *
     * @param  array<string, mixed>  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function post(string $endpoint, array $data = [])
    {
        $payload = array_merge([
            'userid' => $this->credentials->getUsername(),
            'password' => $this->credentials->getPassword(),
            'output' => 'json',
        ], $data);

        return Http::asForm()->withHeaders([
            'apikey' => $this->credentials->getApiKey(),
            'cache-control' => 'no-cache',
        ])->post($this->baseUrl . $endpoint, $payload);
    }

    /**
     * GET $endpoint with the shared userid/password/output=json fields merged in as query params.
     *
     * @param  array<string, mixed>  $query
     * @return \Illuminate\Http\Client\Response
     */
    protected function get(string $endpoint, array $query = [])
    {
        $payload = array_merge([
            'userid' => $this->credentials->getUsername(),
            'password' => $this->credentials->getPassword(),
            'output' => 'json',
        ], $query);

        return Http::withHeaders([
            'apikey' => $this->credentials->getApiKey(),
            'cache-control' => 'no-cache',
        ])->get($this->baseUrl . $endpoint, $payload);
    }
}
