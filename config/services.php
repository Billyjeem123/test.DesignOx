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


    'google' => [
        'client_id' => '1041698225513-nitgtg4a6daeu4g9i89ag4lgn42c9u6k.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'https://designox.iccflifeskills.com.ng/v0.1/api/client/auth/google-callback',
    ],

    'paystack' => [
        'secrete_key' => env('PAYSTACK_SECRET_KEY', 'sk_test_755a02d78f2777eb99ac6ba0b69d8a729b4e1992'),
        'public_key' => env('PAYSTACK_PUBLIC_KEY', 'pk_test_41542043f72482b6dfad6fc342c062ed6088f84c'),
        'url' => env('PAYSTACK_PAYMENT_URL', 'https://designox.iccflifeskills.com.ng/v0.1/api/client/payment/callback'),
    ],

    'app_config' =>[
        'app_mail' => env('APP_MAIL', 'billyhadiattaofeeq@gmail.com'),
        'app_name' => env('APP_NAME', 'DESIGNOX')


]

];
