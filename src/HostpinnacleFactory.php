<?php

namespace Itsmurumba\Hostpinnacle;

use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;

class HostpinnacleFactory
{
    /**
     * Create a Hostpinnacle instance for the given account or credentials.
     *
     * @param  HostpinnacleAccount|HostpinnacleCredentials  $accountOrCredentials
     * @return Hostpinnacle
     */
    public function for(HostpinnacleAccount|HostpinnacleCredentials $accountOrCredentials): Hostpinnacle
    {
        $credentials = $accountOrCredentials instanceof HostpinnacleAccount
            ? $accountOrCredentials->toCredentials()
            : $accountOrCredentials;

        return new Hostpinnacle($credentials);
    }
}
