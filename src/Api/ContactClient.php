<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Contact domain: individual contacts, optionally assigned to a contact group.
 */
class ContactClient extends BaseApiClient
{
    /**
     * Create a contact.
     *
     * @param  array{contactname: string, mobileno: string, groupid?: string, groupname?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function create($data)
    {
        if (!isset($data['contactname']) || !isset($data['mobileno'])) {
            throw new IsNullException('contactname and mobileno must not be null');
        }

        return $this->post('/contact/create', array_filter([
            'contactname' => $data['contactname'],
            'mobileno' => $data['mobileno'],
            'groupid' => $data['groupid'] ?? null,
            'groupname' => $data['groupname'] ?? null,
        ]));
    }

    /**
     * Add or update a contact via the upload endpoint. Despite the name, the API takes the
     * same form fields as create() — there's no file attachment in this endpoint.
     *
     * @param  array{contactname: string, mobileno: string, groupid?: string, groupname?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function upload($data)
    {
        if (!isset($data['contactname']) || !isset($data['mobileno'])) {
            throw new IsNullException('contactname and mobileno must not be null');
        }

        return $this->post('/contact/upload', array_filter([
            'contactname' => $data['contactname'],
            'mobileno' => $data['mobileno'],
            'groupid' => $data['groupid'] ?? null,
            'groupname' => $data['groupname'] ?? null,
        ]));
    }

    /**
     * List contacts on the account.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function read()
    {
        return $this->get('/contact/read');
    }

    /**
     * Update an existing contact.
     *
     * @param  array{id: string, contactname: string, mobileno: string, groupid?: string, groupname?: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function update($data)
    {
        if (!isset($data['id']) || !isset($data['contactname']) || !isset($data['mobileno'])) {
            throw new IsNullException('id, contactname and mobileno must not be null');
        }

        return $this->post('/contact/update', array_filter([
            'id' => $data['id'],
            'contactname' => $data['contactname'],
            'mobileno' => $data['mobileno'],
            'groupid' => $data['groupid'] ?? null,
            'groupname' => $data['groupname'] ?? null,
        ]));
    }

    /**
     * Delete one or more contacts by id (comma-separated).
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

        return $this->post('/contact/delete', [
            'id' => $data['id'],
        ]);
    }
}
