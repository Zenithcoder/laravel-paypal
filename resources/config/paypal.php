<?php

/*
 * This file is part of the Laravel Paypal package.
 *
 * (c) Awonusi Olajide <awonusiolajide@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /**
     * Public Client Id From Paypal Dashboard
     *
     */
    'client_id' => getenv('PAYPAL_CLIENT_ID'),

    /**
     * Secret Key From Paypal Dashboard
     *
     */
    'client_secret' => getenv('PAYPAL_CLIENT_SECRET'),

    /**
     * Paypal Payment URL
     *
     */
    'paymentUrl' => getenv('PAYPAL_PAYMENT_URL'),
];
