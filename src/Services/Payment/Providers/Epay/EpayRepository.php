<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Epay;

use Nurdaulet\FluxWallet\Helpers\PaymentHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EpayRepository
{
    private $baseUrl = 'https://epay-oauth.homebank.kz';
    protected $clientId;
    protected $terminal;
    protected $clientSecret;
    protected $epayEnvIsDev;

    public function __construct()
    {
        $this->epayEnvIsDev = !config('flux-wallet.payment_providers.epay.is_prod');
        $this->clientId = $this->epayEnvIsDev ? config('flux-wallet.payment_providers.epay.dev.client_id') : config('flux-wallet.payment_providers.epay.prod.client_id');
        $this->terminal = $this->epayEnvIsDev ? config('flux-wallet.payment_providers.epay.dev.terminal') : config('flux-wallet.payment_providers.epay.prod.terminal');
        $this->clientSecret = $this->epayEnvIsDev ? config('flux-wallet.payment_providers.epay.dev.client_secret') : config('flux-wallet.payment_providers.epay.prod.client_secret');
    }

    public function getTerminal()
    {
        return $this->terminal;
    }

    public function getToken($invoiceID, $amount)
    {
        $body = [
            'grant_type' => 'client_credentials',
            'scope' => 'transfer',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'invoiceID' => $invoiceID,
            'amount' => $amount,
            'currency' => 'KZT',
            'terminal' => $this->terminal
        ];

        $url = $this->epayEnvIsDev ? 'https://testoauth.homebank.kz/epay2/oauth2/token' : 'https://epay-oauth.homebank.kz/oauth2/token';

        $response = Http::withHeaders(['Accept' => 'application/json'])
            ->asForm()
            ->post($url, $body)
            ->json();
        if (isset($response['access_token']) && $response['access_token']) {
            return $response;
        }
        return abort(400, 'Сервис временно не доступен');
    }

    public function getUrlForCardAddition(int $userId, $amount = 200)
    {

        $data = [
            'user_id' => $userId,
            'amount' => $amount,
            'platform' => request()->header('platform')
        ];
        return route('payments.epay.pay', $data);
    }

    public function pay($user, $transactionId, $tokenData, $amount, $bankcard)
    {

        $postlink = env("APP_URL") . '/api/payments/epay/callback';
        $body = [
            "amount" => $amount,
            "currency" => "KZT",
            "name" => strval($user->name),
            "terminalId" => $this->terminal,
            "invoiceId" => $transactionId,
            "description" => "order",
            "accountId" => $user->id,
            "email" => strval($user->email),
            "phone" => strval($user->phone),
            "backLink" => env("APP_URL") . '/api/payments/success',
            "failureBackLink" => env("APP_URL") . '/api/payments/error',
            "postLink" => $postlink,
            "failurePostLink" => $postlink,
            "language" => "rus",
            "paymentType" => "cardId",
            "cardId" => [
                "id" => $bankcard->card_id,
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenData['access_token'],
        ])->post("https://epay-api.homebank.kz/payments/cards/auth", $body);

        if ($response->status() != 200) {
            return ['transaction_id' => $transactionId, 'response' => $response, 'status' => PaymentHelper::STATUS_FAILED];
        }
        return ['transaction_id' => $transactionId, 'response' => $response, 'status' => PaymentHelper::STATUS_PAID];
    }

    public function revoke($amount, $operationId, $invoiceId)
    {
        if ($amount){
            $url = "https://epay-api.homebank.kz/operation/$operationId/refund?amount=$amount&externalID=$invoiceId";
        }else{
            $url = "https://epay-api.homebank.kz/operation/$operationId/refund";
        }

        $tokenData = $this->getToken($invoiceId, $amount);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenData['access_token'],
        ])
            ->asForm()->post($url);
//        Log::channel('dev')->info(' revoke $response' .json_encode($response));
//        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
//            ->asForm()
//            ->post($url);
        return [];
    }

}
