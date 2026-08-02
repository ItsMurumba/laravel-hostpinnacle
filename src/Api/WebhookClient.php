<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Webhook domain: the endpoint that receives real-time delivery reports (DLR).
 */
class WebhookClient extends BaseApiClient
{
    /**
     * Register a webhook endpoint.
     *
     * @param  array{smswebhook: string, smswebhookrate?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function create($data)
    {
        if (!isset($data['smswebhook'])) {
            throw new IsNullException('smswebhook must not be null');
        }

        return $this->post('/webhook/create', array_filter([
            'smswebhook' => $data['smswebhook'],
            'smswebhookrate' => $data['smswebhookrate'] ?? null,
        ]));
    }

    /**
     * Read the configured webhook.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function read()
    {
        return $this->get('/webhook/read');
    }

    /**
     * Update the webhook endpoint.
     *
     * @param  array{smswebhook: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function update($data)
    {
        if (!isset($data['smswebhook'])) {
            throw new IsNullException('smswebhook must not be null');
        }

        return $this->post('/webhook/update', [
            'smswebhook' => $data['smswebhook'],
        ]);
    }

    /**
     * Remove the configured webhook.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function delete()
    {
        return $this->post('/webhook/delete');
    }
}
