<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Epay;

use Nurdaulet\FluxWallet\Helpers\PaymentHelper;
use Nurdaulet\FluxWallet\Helpers\TransactionHelper;
use Nurdaulet\FluxWallet\Models\Transaction;
use Nurdaulet\FluxWallet\Models\User;
use Nurdaulet\FluxWallet\Repositories\BankcardRepository;
use Nurdaulet\FluxWallet\Services\Payment\Contracts\PaymentProviderContract;
use Illuminate\Support\Facades\Log;

class EpayService implements PaymentProviderContract
{
    public function __construct(
        private EpayRepository     $epayRepository,
        private BankcardRepository $bankcardRepository,
    )
    {
    }

    public function pay($amount, $user, array $params, $transactionId = null)
    {

        $bankcard = $this->bankcardRepository->find($params['bankcard_id']);

        $transactionId = $transactionId ?? $user->getBillableId();
        $tokenData = $this->epayRepository->getToken($transactionId, $amount);
        $response = $this->epayRepository->pay($user, $transactionId, $tokenData, $amount, $bankcard);
        return [$response['transaction_id'], $response['status']];
    }

    public function revoke($amount, $user, $operationId = null)
    {
        $response = $this->epayRepository->revoke($amount, $operationId,$user);
//        $response['body']->transaction_id = $transaction->transaction_id;
        return $response;
    }

    public function getPayPageData($amount, $userId, $platform = null)
    {
        $user = config('flux-wallet.models.user')::find($userId);

        $invoiceID = $user->getBillableId();
        $token = $this->epayRepository->getToken($invoiceID, $amount);
        $data = collect();
        $data->token = $token;
        $data->invoiceID = $invoiceID;
        $data->amount = $amount;
        $data->user_id = $user->id;
        $data->phone = $user->phone;
        $data->email = $user->email;
        $data->terminal = $this->epayRepository->getTerminal();
        $data->platform = $platform;
        return $data;
    }

    public function getUrlForCardAddition($user, $amount = 200)
    {
        return $this->epayRepository->getUrlForCardAddition($user->id, $amount);
    }

    public function callback($data)
    {

        if ($data['code'] == 'ok') {
            $bankcard = $this->bankcardRepository->firstOrCreate(
                [
                    'user_id' => $data['accountId'],
                    'provider' => 'epay',
                    'card_id' => $data['cardId'],
                ],
                [
                    'number' => $data['cardMask'],
                    'bank' => $data['issuer'],
                    'card_owner' => $data['name'],
                ]);
//            $paymentFile = fopen("payment_data.txt", "w") ;
//            fwrite($paymentFile, json_encode($data));
//            fclose($paymentFile);
            $this->handleSaveTransaction($data, $bankcard);
//            Log::channel('dev')->info('jjj revoke' . json_encode($data['data'] && isset(json_decode($data['data'])->type)
//                    && json_decode($data['data'])->type == TransactionHelper::TYPE_ADD_CARD));
            if ($data['data'] && isset(json_decode($data['data'])->type)
                && json_decode($data['data'])->type == TransactionHelper::TYPE_ADD_CARD) {
                $this->epayRepository->revoke($data['amount'], $data['id'], User::findOrFail($data['accountId'])->getBillableId());
            }
        }
    }

    private function handleSaveTransaction($data, $bankcard)
    {
        $transaction = Transaction::where('transaction_id', $data['invoiceId'])
            ->where('user_id', $data['accountId'])
            ->firstOrNew();
        $transaction->transaction_id = $data['invoiceId'];
        $transaction->user_id = $data['accountId'];
        $isReplenish = $transaction->is_replenish;
        $status = $transaction->status;
        if (isset($data['data'])) {
            $transaction->type = $data['data']['type'] ?? TransactionHelper::TYPE_TOP_UP;
            $status = PaymentHelper::STATUS_PAID;
            $isReplenish = true;
        }
        $fieldsJson = array_merge($transaction->fields_json ?? [], ['bankcard_id' => $bankcard->id], ['operation_id' => $data['id']]);
        $transaction->amount = $transaction->amount ?? $data['amount'];
        $transaction->fields_json = $fieldsJson;
        $transaction->type = $transaction->type ?? TransactionHelper::TYPE_NOT_DEFINED;
        $transaction->is_replenish = $isReplenish;
        $transaction->status = $status;
        $transaction->save();
    }
}
