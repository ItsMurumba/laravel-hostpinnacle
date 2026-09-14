<?php

namespace Itsmurumba\Hostpinnacle\Api;

/**
 * API Key domain: manage the API key used to authenticate SMS/API requests.
 */
class ApiKeyClient extends BaseApiClient
{
    /**
     * Generate a new API key.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function create()
    {
        return $this->post('/apikey/create');
    }

    /**
     * Read the current API key.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function read()
    {
        return $this->get('/apikey/read');
    }

    /**
     * Regenerate the API key.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function update()
    {
        return $this->post('/apikey/update');
    }

    /**
     * Revoke the API key.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function delete()
    {
        return $this->post('/apikey/delete');
    }
}
