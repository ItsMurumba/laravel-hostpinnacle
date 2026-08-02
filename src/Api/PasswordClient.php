<?php

namespace Itsmurumba\Hostpinnacle\Api;

use Itsmurumba\Hostpinnacle\Exceptions\IsNullException;

/**
 * Password domain: change the Hostpinnacle portal password.
 */
class PasswordClient extends BaseApiClient
{
    /**
     * Change the account password.
     *
     * @param  array{newpassword: string, confirmpassword: string}  $data
     * @return \Illuminate\Http\Client\Response
     * @throws IsNullException
     */
    public function change($data)
    {
        if (!isset($data['newpassword']) || !isset($data['confirmpassword'])) {
            throw new IsNullException('newpassword and confirmpassword must not be null');
        }

        return $this->post('/password/change', [
            'newpassword' => $data['newpassword'],
            'confirmpassword' => $data['confirmpassword'],
        ]);
    }
}
