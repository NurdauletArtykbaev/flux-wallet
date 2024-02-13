<?php


return [

    'models' => [
        'bankcard' => \Nurdaulet\FluxWallet\Models\Bankcard::class,
        'transaction' => \Nurdaulet\FluxWallet\Models\Transaction::class,
        'user' => \Nurdaulet\FluxWallet\Models\User::class,
        'balance' => \Nurdaulet\FluxWallet\Models\Balance::class,
    ],
    'payment_providers' => [
        'epay' => [
            'prod' => [
                'client_id' => env('EPAY_CLIENT_ID'),
                'terminal' => env('EPAY_TERMINAL'),
                'client_secret' => env('EPAY_CLIENT_SECRET')
            ],
            'dev' => [
                'client_id' => env('EPAY_DEV_CLIENT_ID'),
                'terminal' => env('EPAY_DEV_TERMINAL'),
                'client_secret' => env('EPAY_DEV_CLIENT_SECRET')
            ],
            'env' => env('EPAY_ENV', "LOCAL") == "LOCAL" ,
            'is_prod' => env('EPAY_ENV', "LOCAL") != "LOCAL" ,

        ],
        'cloudpayments' => [
            'public' => env('CLOUDPAYMENT_PUBLIC'),
            'private' => env('CLOUDPAYMENT_PRIVATE'),
        ],
        'one_vision' => [
            'secret' => env('ONE_VISION_SECRET'),
            'apiKey' => env('ONE_VISION_KEY')
        ]
    ],
    'languages' => [
        'ru', 'en', 'kk'
    ],
    'options' => [
        'payment_provider' => 'epay',// выделить на env file
        'cache_expiration' => 269746,
    ],
];
