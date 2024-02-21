<?php

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /**
     * Public Key From Paystack Dashboard
     *
     */
    'publicKey' => 'pk_test_41542043f72482b6dfad6fc342c062ed6088f84c',

    /**
     * Secret Key From Paystack Dashboard
     *
     */
    'secretKey' => 'sk_test_755a02d78f2777eb99ac6ba0b69d8a729b4e1992',

    /**
     * Paystack Payment URLe
     * 8J+BY7gx[6yg2K
     *
     */
    'paymentUrl' => 'https://designox.iccflifeskills.com.ng/v0.1/api/google-redirect',

    /**
     * Optional email address of the merchant
     *
     */
    'merchantEmail' => getenv('MERCHANT_EMAIL'),

];
