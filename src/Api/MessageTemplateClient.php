<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Message Template domain: reusable message bodies for sending.
 */
class MessageTemplateClient extends BaseApiClient
{
    /**
     * Create a message template.
     *
     * @param  array{message: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function create($data)
    {
        if (!isset($data['message'])) {
            throw new IsNullException('message must not be null');
        }

        return $this->post('/template/create', [
            'message' => $data['message'],
        ]);
    }

    /**
     * List message templates on the account.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function read()
    {
        return $this->get('/template/read');
    }

    /**
     * Update an existing message template.
     *
     * @param  array{message: string, id: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function update($data)
    {
        if (!isset($data['message']) || !isset($data['id'])) {
            throw new IsNullException('message and id must not be null');
        }

        return $this->post('/template/update', [
            'message' => $data['message'],
            'id' => $data['id'],
        ]);
    }

    /**
     * Delete one or more message templates by id (comma-separated).
     *
     * @param  array{id: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function delete($data)
    {
        if (!isset($data['id'])) {
            throw new IsNullException('id must not be null');
        }

        return $this->post('/template/delete', [
            'id' => $data['id'],
        ]);
    }
}
