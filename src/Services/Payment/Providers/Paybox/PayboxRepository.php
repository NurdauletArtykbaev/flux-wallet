<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Paybox;

use Nurdaulet\FluxWallet\Facades\StringFormatter;
use Nurdaulet\FluxWallet\Helpers\PaymentHelper;
use Illuminate\Support\Facades\Http;

class PayboxRepository
{
    private $client;
    private $key;
    private $merchantId;
    private $baseUrl = 'https://api.paybox.money';

    public function __construct()
    {
        $this->merchantId = config('flux-wallet.payment_providers.paybox.default.merchantId');
        $this->key = config('flux-wallet.payment_providers.paybox.default.secretKey');
        $this->client = Http::baseUrl($this->baseUrl);
    }

    public function getUrlForCardAddition(int $userId, $amount = null)
    {
        $this->setMerchant();
        $params = [
            'pg_user_id' => "$userId",
//            'pg_post_link' => $base,
            'pg_post_link' => str_replace('http://localhost', config('app.url'), route('bankcards.store')),
            'pg_back_link' => 'https://eclubusiness.com/success',
//            'pg_back_link' => 'https://europharma.kz',
        ];


        $response = $this->sendRequest("v1/merchant/$this->merchantId/cardstorage/add", $params);

        return $response['body']->pg_redirect_url;
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

                    abort(400,$message);
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

                    abort(400,$message);
                }
            }
        } catch (\Exception $e) {


            \Log::channel('dev')->error('Error while parsing response from Paybox', [
                'url' => $url,
                'params' => json_encode($params),
                'response' => $response ,
            ]);
            abort($e->getCode(),$e->getMessage());
        }

        return [
            'status' => $this->isSuccessful($response),
            'body' => $response
        ];
    }

    private function isResponseHtml($response): bool
    {
        return is_string($response->body()) && str_contains($response->body(),'<form');
    }

    public function initPayment($amount, $transactionable, $bankcard, $type = null)
    {
        $this->setMerchant($bankcard->city_id);
        $params = [
            'pg_user_id'     => "{$bankcard->user_id}",
            'pg_amount'      => $amount,
            'pg_order_id'    => (string)$transactionable->getBillableId(),
            'pg_card_id'     => $bankcard->card_id,
            'pg_description' => "Оплата по заказу №" . $transactionable->getBillableId(),
            'pg_result_url'  =>\App::isProduction()
                ? str_replace('http://localhost', config('app.url'), route('payment.callback'))
                : route('payment.callback'),
            'pg_success_url' =>\App::isProduction()
                ? str_replace('http://localhost', config('app.url'), route('payment.callback'))
                : route('payment.callback'),
            'pg_failure_url' =>\App::isProduction()
                ? str_replace('http://localhost', config('app.url'), route('payment.callback'))
                : route('payment.callback'),
        ];
        $params['payment_type'] = 'top-uo';
        $response = $this->sendRequest("v1/merchant/$this->merchantId/card/init", $params);
        return $response['body']->pg_payment_id;
    }

    public function pay($transactionId)
    {
        $params = [
            'pg_payment_id' => $transactionId,
        ];
        $response = $this->sendRequest("v1/merchant/$this->merchantId/card/pay", $params, false);

        return ['transaction_id' => $transactionId, 'response' => $response];
    }

    public function revoke($amount, $transaction)
    {
        $transaction->load('bankcard');
        $params = [
            'pg_payment_id' => "{$transaction->transaction_id}",
            'pg_refund_amount' => $amount,
            'payment_type' => 'revoke'
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
