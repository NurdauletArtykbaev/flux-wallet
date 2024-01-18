<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\CloudPayments;

use Nurdaulet\FluxWallet\Models\Transaction;
use Nurdaulet\FluxWallet\Services\Payment\Contracts\PaymentProviderContract;

class CloudPaymentsService implements PaymentProviderContract
{
    public function __construct()
    {
    }

    public function pay($amount, $user, $params, $transactionId = null)
    {
        $responses = collect([
            [null],
            ['transaction_id' => rand(100000000, 9999999999)]
        ]);

        return $responses->random();
    }
    public function revoke($amount,Transaction  $transaction, $revokeTransaction)
    {
    }
    public function callback($data)
    {
    }
    public function getUrlForCardAddition($user, $amount)
    {
    }
}
