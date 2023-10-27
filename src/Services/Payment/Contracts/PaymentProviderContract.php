<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Contracts;

use Nurdaulet\FluxWallet\Models\Transaction;

interface PaymentProviderContract
{
    public function pay($amount, $user, array $params);
    public function revoke($amount, $user, Transaction $transaction);
    public function getUrlForCardAddition($user, $amount);
    public function callback($data);
}
