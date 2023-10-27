<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Paybox\Facades;

use Illuminate\Support\Facades\Facade;

class Paybox extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'payboxService';
    }
}
