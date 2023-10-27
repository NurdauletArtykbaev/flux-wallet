<?php

namespace Nurdaulet\FluxWallet\Facades;

use Illuminate\Support\Facades\Facade;

class StringFormatter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'stringFormatter';
    }
}
