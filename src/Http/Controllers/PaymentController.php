<?php

namespace Nurdaulet\FluxWallet\Http\Controllers;

use Nurdaulet\FluxWallet\Http\Requests\Payment\EpayPayPageRequest;
use Nurdaulet\FluxWallet\Services\Payment\Facades\Payment;
use Nurdaulet\FluxWallet\Services\Payment\Providers\Epay\EpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController 
{
    public function __construct(private EpayService $epayService)
    {
    }

    public function payPage(EpayPayPageRequest $request)
    {
        $platform = $request->header('platform');
        $amount = $request->input('amount', 2000);

        $data = $this->epayService->getPayPageData($amount,$request->user_id, $platform);

        return view('payment.epay.pay', ['data' => $data]);
    }


    public function epayCallback(Request $request)
    {
        $content = $request->all();
        Log::channel('dev')->info('payment callback: ' . json_encode($content));

        Payment::callback('epay', $content);
        header('HTTP/1.1 200 OK');
        $out = true;
        echo '{"accepted":' . $out . '}';
    }


    public function success(Request $request)
    {
        $message = $request->message ?? 'Успешно оплачено!';
        $data = [
            'success' => true,
            'message' => $message,
        ];
        return view('payment.success', [
            'data' => $data
        ]);
    }


    public function error(Request $request)
    {
        Log::channel('dev')->info('pay error ' . json_encode($request->all()));
        $message = $request->message ?? 'Платеж отклонен!';
        $data = [
            'success' => false,
            'message' => $message,
        ];
        return view('payment.error', [
            'data' => $data,
        ]);
    }
}
