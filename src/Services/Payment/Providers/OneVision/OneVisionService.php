<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\OneVision;

use Nurdaulet\FluxWallet\Models\Transaction;
use Nurdaulet\FluxWallet\Services\Payment\Contracts\PaymentProviderContract;

class OneVisionService implements PaymentProviderContract
{
    public function __construct(private OneVisionRepository $oneVisionRepository)
    {
    }

    public function pay($amount, $user, array $params, $transactionId = null)
    {
        // TODO: Implement pay() method.
    }


    public function getUrlForCardAddition($user, $amount = null)
    {
        return $this->oneVisionRepository->getUrlForCardAddition($user->id, $amount);
    }


    public function revoke($amount, Transaction $transaction, $revokeTransaction)
    {
    }

    public function callback($data)
    {
    }
}
