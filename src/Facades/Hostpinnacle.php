<?php

namespace Itsmurumba\Hostpinnacle\Facades;

use Illuminate\Support\Facades\Facade;
use Itsmurumba\Hostpinnacle\Hostpinnacle as HostpinnacleClient;
use Itsmurumba\Hostpinnacle\HostpinnacleFactory;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;

/**
 * Facade for the Hostpinnacle SMS client. Use for single-account (config) or Hostpinnacle::for($account) for SaaS.
 *
 * @method static \Illuminate\Http\Client\Response sendQuickSMS(array $data)
 * @method static \Illuminate\Http\Client\Response sendQuickScheduledSMS(array $data)
 * @method static \Illuminate\Http\Client\Response sendGroupSMS(array $data)
 * @method static \Illuminate\Http\Client\Response sendGroupScheduledSMS(array $data)
 * @method static \Illuminate\Http\Client\Response sendMobileOnlyFileSMS(array $data)
 * @method static \Illuminate\Http\Client\Response sendMobileOnlyFileScheduledSMS(array $data)
 * @method static \Illuminate\Http\Client\Response sendMobileAndMessageFileSMS(array $data)
 * @method static \Illuminate\Http\Client\Response sendMobileAndMessageFileScheduledSMS(array $data)
 * @method static HostpinnacleClient for(HostpinnacleAccount|\Itsmurumba\Hostpinnacle\HostpinnacleCredentials $accountOrCredentials)
 *
 * @see \Itsmurumba\Hostpinnacle\Hostpinnacle
 * @see \Itsmurumba\Hostpinnacle\HostpinnacleFactory
 */
class Hostpinnacle extends Facade
{
    /**
     * Get the facade accessor (container key).
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hostpinnacle';
    }

    /**
     * Create a Hostpinnacle instance for the given account or credentials (SaaS multi-account).
     *
     * @param  HostpinnacleAccount|\Itsmurumba\Hostpinnacle\HostpinnacleCredentials  $accountOrCredentials
     * @return HostpinnacleClient
     */
    public static function for(HostpinnacleAccount|\Itsmurumba\Hostpinnacle\HostpinnacleCredentials $accountOrCredentials): HostpinnacleClient
    {
        return app(HostpinnacleFactory::class)->for($accountOrCredentials);
    }
}
