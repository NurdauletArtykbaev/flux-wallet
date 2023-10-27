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
            'almaty' => [
                'public' => 'pk_833eeeb811a850b4d9f8f1ce20cb9',
                'private' => 'd8d1173df0c9819acefd01927f2a882a',
            ]
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
        'use_filament_admin_panel' => true,
        'cache_expiration' => 269746,
    ],
];
