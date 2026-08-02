<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Contact Group domain: named groups of contacts (the same "group" sendGroupSMS() sends to).
 * Named ContactGroupClient, not GroupClient, to read distinctly from the sending verb.
 */
class ContactGroupClient extends BaseApiClient
{
    /**
     * Create a contact group.
     *
     * @param  array{groupname: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function create($data)
    {
        if (!isset($data['groupname'])) {
            throw new IsNullException('groupname must not be null');
        }

        return $this->post('/group/create', [
            'groupname' => $data['groupname'],
        ]);
    }

    /**
     * List contact groups on the account.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function read()
    {
        return $this->get('/group/read');
    }

    /**
     * Rename an existing contact group.
     *
     * @param  array{groupname: string, id: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function update($data)
    {
        if (!isset($data['groupname']) || !isset($data['id'])) {
            throw new IsNullException('groupname and id must not be null');
        }

        return $this->post('/group/update', [
            'groupname' => $data['groupname'],
            'id' => $data['id'],
        ]);
    }

    /**
     * Delete one or more contact groups, by id or by group name (comma-separated).
     *
     * @param  array{id?: string, groupname?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function delete($data)
    {
        if (!isset($data['id']) && !isset($data['groupname'])) {
            throw new IsNullException('id or groupname must not be null');
        }

        return $this->post('/group/delete', array_filter([
            'id' => $data['id'] ?? null,
            'groupname' => $data['groupname'] ?? null,
        ]));
    }
}
