<?php

namespace Nurdaulet\FluxWallet\Services\Payment;

use Nurdaulet\FluxWallet\Helpers\PaymentHelper;
use Nurdaulet\FluxWallet\Helpers\TransactionHelper;
use Nurdaulet\FluxWallet\Interfaces\IBillable;
use Nurdaulet\FluxWallet\Models\Transaction;
use Nurdaulet\FluxWallet\Models\User;
use Nurdaulet\FluxWallet\Services\Payment\Providers\Epay\Facades\Epay;
use Nurdaulet\FluxWallet\Services\Payment\Providers\OneVision\Facades\OneVision;
use Nurdaulet\FluxWallet\Services\Payment\Providers\Paybox\Facades\Paybox;

class PaymentService
{

    public function __construct()
    {
    }

    public function pay($amount, $user, array $params)
    {
        $user = User::findOrFail($user->id);
        $user->load('balance');
        [$transactionId, $transactionStatus] = $this->getPaymentService()::pay($amount, $user, $params, $params['transaction_id'] ?? null);
        $data = [
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'fields_json' => $params,
            'type' => $params['type'] ?? TransactionHelper::TYPE_NOT_DEFINED,
            'status' => $transactionStatus
        ];
        if ($params['type'] == TransactionHelper::TYPE_TOP_UP) {
            $data['is_replenish'] = true;
        }
        $user->transactions()->create($data);
    }

    public function revoke($amount, $transaction, $revokeTransactionId)
    {
        $fieldsJson = $transaction->fields_json?->bankcard_id ? ['bankcard_id' => $transaction->fields_json?->bankcard_id] : [];
        $revokeTransaction = Transaction::create([
            'user_id' => $transaction->user_id,
            'transaction_id' => $revokeTransactionId,
            'type' => TransactionHelper::TYPE_REFUND,
            'status' => PaymentHelper::STATUS_PENDING,
            'amount' => $amount,
            'fields_json' => $fieldsJson
        ]);
        return $this->getPaymentService()::revoke($amount, $transaction, $revokeTransaction);
    }

    public function callback($provider, $data)
    {
        return $this->getPaymentService($provider)::callback($data);
    }

    public function getUrlForCardAddition($user, $amount = 200)
    {
        return $this->getPaymentService()::getUrlForCardAddition($user, $amount);
    }

    private function getPaymentService($provider = null)
    {
        if (empty($provider)) {
            $provider = config('flux-wallet.options.payment_provider');
        }
        return match ($provider) {
            'paybox' => Paybox::class,
            'one_vision' => OneVision::class,
            'epay' => Epay::class,
            default => Epay::class
        };

    }

}
