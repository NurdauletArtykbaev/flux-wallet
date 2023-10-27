<?php

namespace Nurdaulet\FluxWallet\Services\Payment;

use Nurdaulet\FluxWallet\Helpers\TransactionHelper;
use Nurdaulet\FluxWallet\Interfaces\IBillable;
use Nurdaulet\FluxWallet\Services\Payment\Providers\Epay\Facades\Epay;
use Nurdaulet\FluxWallet\Services\Payment\Providers\OneVision\Facades\OneVision;
use Nurdaulet\FluxWallet\Services\Payment\Providers\Paybox\Facades\Paybox;

class PaymentService
{

    public function __construct()
    {
    }

    public function pay($amount,  $user, array $params)
    {
        $provider = config('flux-wallet.options.payment_provider');

        $paymentService = match ($provider) {
            'paybox' => Paybox::class,
            'one_vision' => OneVision::class,
            'epay' => Epay::class,
            default => Epay::class
        };

        $user->load('balance');

        [$transactionId, $transactionStatus] = $paymentService::pay($amount,$user, $params);
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

    public function revoke($amount, IBillable $billable, $transaction, $orderRefund)
    {
        $provider = config('flux-wallet.options.payment_provider');
        $paymentService = match ($provider) {
            'paybox' => Paybox::class,
            'one_vision' => OneVision::class,
            'epay' => Epay::class,
        };
        $response = $paymentService::revoke($amount, $billable, $transaction, $orderRefund);

//        $params = [
//            'pg_revoke_payment_id' => $response['body']->pg_revoke_payment_id,
//            'cost' => $amount,
//            'number' => $billable->number,
//            'return_id' => $orderRefund->return_id,
//            'status' => $response['status']
//        ];
//        if (!$response['status']) {
//            $params['message'] = $response['body']->pg_failure_description;
//        }
//
//        $billable->transactions()->create([
//            'transaction_id' => $response['body']->transaction_id,
//            'fields_json' => $params,
//            'status' => $params['status'] ? PaymentHelper::STATUS_REFUND : PaymentHelper::STATUS_REFUND_FAILED,
//        ]);

        return $response;
    }

    public function callback($provider, $data)
    {
//        $provider = config('flux-wallet.options.payment_provider');
        $paymentService = match ($provider) {
            'paybox' => Paybox::class,
            'one_vision' => OneVision::class,
            'epay' => Epay::class,
        };
        return $paymentService::callback($data);
    }

    public function getUrlForCardAddition($user, $amount = 2000)
    {
        $provider = config('flux-wallet.options.payment_provider');
        $paymentService = match ($provider) {
            'paybox' => Paybox::class,
            'one_vision' => OneVision::class,
            'epay' => Epay::class,
        };
        return $paymentService::getUrlForCardAddition($user, $amount);
    }

}
