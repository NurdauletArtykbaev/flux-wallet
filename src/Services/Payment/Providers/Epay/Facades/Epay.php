<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Epay\Facades;

use Illuminate\Support\Facades\Facade;

class Epay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'epayService';
    }
}
