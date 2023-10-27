<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\CloudPayments;

use Nurdaulet\FluxWallet\Services\Payment\Contracts\PaymentProviderContract;

class CloudPaymentsService implements PaymentProviderContract
{
    public function __construct()
    {
    }

    public function pay($amount, $user, $params)
    {
        $responses = collect([
            [null],
            ['transaction_id' => rand(100000000, 9999999999)]
        ]);

        return $responses->random();
    }
    public function revoke($amount, $user,  $transaction)
    {
    }
    public function callback($data)
    {
    }
    public function getUrlForCardAddition($user, $amount)
    {
    }
}
