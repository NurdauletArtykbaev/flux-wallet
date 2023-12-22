<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

</body>
@if(!config('flux-wallet.payment_providers.epay.is_prod'))
    <script src="https://test-epay.homebank.kz/payform/payment-api.js"></script>
@else
    <script src="https://epay.homebank.kz/payform/payment-api.js"></script>
@endif
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script>

    let data = {
        access_token: "{{ $data->token['access_token'] }}",
        expires_in: "{{ $data->token['expires_in'] }}",
        refresh_token: "{{ $data->token['refresh_token'] }}",
        scope: "{{ $data->token['scope'] }}",
        token_type: "{{ $data->token['token_type'] }}"
    }
    let success_url = "{{ $data->platform == Nurdaulet\FluxWallet\Helpers\DeviceTokenHelper::PLATFORM_WEB
                                ? (env("SITE_URL") . '/payment/cards/')
                                 : (env("APP_URL") . '/api/payments/success')}}"
    let paymentObject = {
        invoiceId: "{{ $data->invoiceID }}",
        backLink: success_url,
        failureBackLink: "{{ env("APP_URL") }}" + '/api/payments/error',
        postLink: "{{ env("APP_URL") }}" + '/api/payments/epay/callback',
        failurePostLink: "failure.html",
        language: "RU",
        description: "Оплата в интернет магазине",
        accountId: "{{ $data->user_id }}",
        terminal: "{{ $data->terminal }}",
        amount: "{{$data->amount}}",
        currency: "KZT",
        phone: "{{ $data->phone }}",
        data: "{\"type\": \"{{Nurdaulet\FluxWallet\Helpers\TransactionHelper::TYPE_ADD_CARD}}\"}",
        email: "{{ $data->email }}",
        cardSave: "true",
        auth: data
    };
    halyk.pay(paymentObject);
</script>
</html>
