<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Contracts;

use Nurdaulet\FluxWallet\Models\Transaction;

interface PaymentProviderContract
{
    public function pay($amount, $user, array $params,  $transactionId = null);
    public function revoke($amount, Transaction $transaction, $revokeTransaction);
    public function getUrlForCardAddition($user, $amount);
    public function callback($data);
}
