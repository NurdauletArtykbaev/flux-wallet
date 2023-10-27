<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Paybox;

use Nurdaulet\FluxWallet\Repositories\BankcardRepository;
use Illuminate\Support\ServiceProvider;

class PayboxServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register(); // TODO: Change the autogenerated stub
        $this->app->bind('payboxService', function () {
            return new PayboxService(new PayboxRepository(), new BankcardRepository());
        });
    }
}
