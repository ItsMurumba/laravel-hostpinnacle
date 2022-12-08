<?php

namespace Itsmurumba\Hostpinnacle\Facades;

use Illuminate\Support\Facades\Facade;

class Hostpinnacle extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'hostpinnacle';
    }
}
