<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\OneVision\Facades;

use Illuminate\Support\Facades\Facade;

class OneVision extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'oneVisionService';
    }
}
