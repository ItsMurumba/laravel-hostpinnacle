<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Sender ID domain: manage the approved sender names available to the account.
 */
class SenderIdClient extends BaseApiClient
{
    /**
     * Request a new sender ID.
     *
     * @param  array{senderid: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function create($data)
    {
        if (!isset($data['senderid'])) {
            throw new IsNullException('senderid must not be null');
        }

        return $this->post('/senderid/create', [
            'senderid' => $data['senderid'],
        ]);
    }

    /**
     * List sender IDs on the account.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function read()
    {
        return $this->get('/senderid/read');
    }

    /**
     * Rename an existing sender ID.
     *
     * @param  array{senderid: string, id: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function update($data)
    {
        if (!isset($data['senderid']) || !isset($data['id'])) {
            throw new IsNullException('senderid and id must not be null');
        }

        return $this->post('/senderid/update', [
            'senderid' => $data['senderid'],
            'id' => $data['id'],
        ]);
    }

    /**
     * Delete one or more sender IDs, by id or by sender name (comma-separated).
     *
     * @param  array{id?: string, senderid?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function delete($data)
    {
        if (!isset($data['id']) && !isset($data['senderid'])) {
            throw new IsNullException('id or senderid must not be null');
        }

        return $this->post('/senderid/delete', array_filter([
            'id' => $data['id'] ?? null,
            'senderid' => $data['senderid'] ?? null,
        ]));
    }
}
