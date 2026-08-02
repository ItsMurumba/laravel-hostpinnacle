<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Account Profile domain: the remote Hostpinnacle account's status, profile, and credit
 * history. Not Models\HostpinnacleAccount, which is this package's own SaaS storage.
 */
class AccountProfileClient extends BaseApiClient
{
    /**
     * Read the account's status.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function readStatus()
    {
        return $this->get('/account/readstatus');
    }

    /**
     * Read the account's profile.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function readProfile()
    {
        return $this->get('/account/readprofile');
    }

    /**
     * Update any subset of the account's profile fields.
     *
     * @param  array{fullname?: string, address?: string, region?: string, country?: string, city?: string, profilepic?: string}  $data
     * @return \Illuminate\Http\Client\Response
     */
    public function updateProfile($data)
    {
        return $this->post('/account/updateprofile', array_filter([
            'fullname' => $data['fullname'] ?? null,
            'address' => $data['address'] ?? null,
            'region' => $data['region'] ?? null,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'profilepic' => $data['profilepic'] ?? null,
        ]));
    }

    /**
     * Read the account's credit history within a date range.
     *
     * @param  array{fromdate: string, todate: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function readCreditHistory($data)
    {
        if (!isset($data['fromdate']) || !isset($data['todate'])) {
            throw new IsNullException('fromdate and todate must not be null');
        }

        return $this->post('/account/readcredithistory', [
            'fromdate' => $data['fromdate'],
            'todate' => $data['todate'],
        ]);
    }
}
