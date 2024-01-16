<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Paybox;

use Nurdaulet\FluxWallet\Facades\StringFormatter;
use Nurdaulet\FluxWallet\Repositories\BankcardRepository;
use Nurdaulet\FluxWallet\Services\Payment\Contracts\PaymentProviderContract;
use Nurdaulet\FluxWallet\Services\Payment\Providers\Paybox\Classes\CardData;

class PayboxService implements PaymentProviderContract
{
    public function __construct(
        private PayboxRepository   $payboxRepository,
        private BankcardRepository $bankcardRepository,
    )
    {
    }

    public function pay($amount, $user, array $params, $transactionId = null)
    {
        $bankcard = $this->bankcardRepository->find($params['bankcard_id']);

        $transactionId = $this->payboxRepository->initPayment($amount, $user, $bankcard, $params['payment_type'] ?? null);
        $response = $this->payboxRepository->pay($transactionId);
        return ['transaction_id' => $response['transaction_id'], 'response' => $response['response']];
    }

    public function revoke($amount, $user, $transaction)
    {
        $response = $this->payboxRepository->revoke($amount, $transaction);
        $response['body']->transaction_id = $transaction->transaction_id;
        return $response;
    }

    public function getUrlForCardAddition($user, $amount = 200)
    {
        return $this->payboxRepository->getUrlForCardAddition($user->id, $amount);
    }

    public function callback($data)
    {
        /** handle data */
    }

    public function addCard($payload)
    {
        $payload = StringFormatter::parseXml($payload['pg_xml']);
        $cardData = new CardData($payload);
        return $this->bankcardRepository->firstOrCreate($cardData->toModel(), $cardData->toModel());
    }
}
