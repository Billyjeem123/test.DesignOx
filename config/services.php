<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // 'google' => [
    //     'client_id' => env('GOOGLE_CLIENT_ID'),
    //     'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    //     'redirect' => env('GOOGLE_CALLBACK_URL'),
    // ]

    'google' => [
        'client_id' => '358463868849-makltra3iitnee9iu32hkb7tb9voi1jb.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'http://127.0.0.1:8000/v0.1/api/auth/google-callback',
    ],

    'paystack' => [
        'secrete_key' => env('PAYSTACK_SECRET_KEY', 'sk_test_755a02d78f2777eb99ac6ba0b69d8a729b4e1992'),
        'public_key' => env('PAYSTACK_PUBLIC_KEY', 'pk_test_41542043f72482b6dfad6fc342c062ed6088f84c'),
        'url' => env('PAYSTACK_PAYMENT_URL', 'https://iccflifeskills.com.ng/v0.1/api/payment/callback'),
    ],

    'app_config' =>[
        'app_mail' => env('APP_MAIL', 'billyhadiattaofeeq@gmail.com')

]

];
