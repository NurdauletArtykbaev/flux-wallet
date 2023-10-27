<?php

namespace Nurdaulet\FluxWallet\Services;

use Nurdaulet\FluxWallet\Helpers\TransactionHelper;
use Nurdaulet\FluxWallet\Repositories\BankcardRepository;
use Nurdaulet\FluxWallet\Services\Payment\Facades\Payment;

class BankcardService
{
    public function __construct(private BankcardRepository $bankcardRepository)
    {
    }

    public function topUp($user, $bankcardId, $amount)
    {
        $bankcard = $this->bankcardRepository->find($bankcardId, ['user_id' => $user->id]);
        $params = [
            'bankcard_id' => $bankcard->id,
            'type' => TransactionHelper::TYPE_TOP_UP
        ];
        Payment::pay($amount, $user, $params);
    }

    public function delete($user, $bankcardId)
    {
        $bankcard = $this->bankcardRepository->find($bankcardId, ['user_id' => $user->id]);
        $bankcard->delete();
    }

}
