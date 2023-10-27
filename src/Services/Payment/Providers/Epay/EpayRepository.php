<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Epay;

use Nurdaulet\FluxWallet\Helpers\PaymentHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Nurdaulet\FluxWallet\Facades\StringFormatter;

class EpayRepository
{
    private $client;
    private $baseUrl = 'https://api.paybox.money';
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
        $this->client = Http::baseUrl($this->baseUrl);
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

        $response = Http::withHeaders(['Accept' =>'application/json' ])
            ->asForm()
            ->post($url, $body)->json();
        if (isset($response['access_token']) && $response['access_token']) {
            return $response;
        }
        return  abort(400,'Сервис временно не доступен');
    }



    public function getUrlForCardAddition(int $userId, $amount = 200)
    {

        $data =  [
            'user_id' => $userId,
            'amount' => $amount,
            'platform' => request()->header('platform')
        ];
        return route('payments.epay.pay', $data);
    }

    private function sendRequest($url, $params, $throw = true)
    {
        $params['pg_merchant_id'] = $this->merchantId;
        $params['pg_salt'] = "sAWumVI6p37o2TLS";
        $params['pg_testing_mode'] = 0;
        $operation = explode('/', $url);
        $operation = end($operation);

        ksort($params);
        array_unshift($params, $operation);
        $params[] = $this->key;
        $params['pg_sig'] = md5(implode(';', $params));

        unset($params[0], $params[1]);

        $response = $this->client->post($url, $params);

        try {
            if ($this->isResponseHtml($response)) {

                $response = StringFormatter::parsePayboxErrorHtml($response->body());

                if (isset($response["pg_failure_description"])) {
                    $message = $response["pg_failure_description"] . "." .
                        (!str_contains(request()->getUri(), '/pay')
                            ? ' ' . trans('text.go_to_my_orders')
                            : '');

                    abort(400,$message );
                }
                return [
                    'status' => 'ok',
                    'body' => $response
                ];

            } else {
                $response = StringFormatter::parseXml($response->body());

                if (!$this->isSuccessful($response)) {
                    if (isset($response->pg_failure_description)) {
                        $message = $response->pg_failure_description .
                            (!str_contains(request()->getUri(), '/pay')
                                ? ' ' . trans('text.go_to_my_orders')
                                : '');
                    } else {
                        $message = $response->pg_error_description . "." .
                            (!str_contains(request()->getUri(), '/pay')
                                ? ' ' . trans('text.go_to_my_orders')
                                : '');
                    }

                    abort(400,$message );
                }
            }
        } catch (\Exception $e) {


            \Log::channel('dev')->error('Error while parsing response from Paybox', [
                'url' => $url,
                'params' => json_encode($params),
                'response' => $response,
            ]);
            abort($e->getCode(),$e->getMessage() );
        }

        return [
            'status' => $this->isSuccessful($response),
            'body' => $response
        ];
    }

    private function isResponseHtml($response): bool
    {
        return is_string($response->body()) && str_contains($response->body(), '<form');
    }


    public function pay($user, $transactionId, $tokenData, $amount, $bankcard)
    {

        $postlink = env("APP_URL") . '/api/v1/payments/epay/callback';
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
            "backLink" => env("APP_URL") . '/api/v1/payments/success',
            "failureBackLink" => env("APP_URL") . '/api/v1/payments/error',
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

    public function revoke($amount, $transaction)
    {
        $transaction->load('bankcard');
        if ($transaction->bankcard && $transaction->bankcard->city_id) {
            $this->setMerchant($transaction->bankcard->city_id);
        }
        $params = [
            'pg_payment_id' => "{$transaction->transaction_id}",
            'pg_refund_amount' => $amount,
        ];
        $response = $this->sendRequest("revoke.php", $params);
        return $response;
    }

    public function getStatus($transactionId)
    {
        $response = $this->sendRequest('/get_status2.php', ['pg_payment_id' => $transactionId]);

        return $response['body']->pg_payment_id;
    }

    private function setMerchant($cityId)
    {
        $merchant = PaymentHelper::getMerchant($cityId);
        $this->merchantId = $merchant['merchantId'];
        $this->key = $merchant['secretKey'];
    }

    private function isSuccessful($response): bool
    {
        return $response->pg_status === 'ok';
    }

}
