<?php

namespace Nurdaulet\FluxWallet\Facades;

use Illuminate\Support\Facades\Facade;

class TextConverter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'textConverter';
    }
}
