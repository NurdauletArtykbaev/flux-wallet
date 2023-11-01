<?php

namespace Nurdaulet\FluxWallet\Services;

use Nurdaulet\FluxWallet\Helpers\PaymentHelper;
use Nurdaulet\FluxWallet\Helpers\TransactionHelper;

class WalletFacadeService
{
    public function getBalanceByUserId($userId)
    {
        return config('flux-wallet.models.balance')::firstOrCreate(['user_id' => $userId]);
    }
}
