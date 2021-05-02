# Laravel PayPal

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/zenithcoder/paypal.svg?style=flat-square)](https://packagist.org/packages/zenithcoder/laravel-paypal)
[![Total Downloads](https://img.shields.io/packagist/dt/zenithcoder/paypal.svg?style=flat-square)](https://packagist.org/packages/zenithcoder/laravel-paypal)
[![StyleCI](https://github.styleci.io/repos/43671533/shield?branch=v2.0)](https://github.styleci.io/repos/43671533?branch=v2.0)
![Tests](https://github.com/zenithcoder/laravel-paypal/workflows/TestsV3/badge.svg)


- [Introduction](#introduction)
- [PayPal API Credentials](#paypal-api-credentials)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Support](#support)

    
<a name="introduction"></a>
## Introduction

By using this library you can process payment from PayPal and listen to events in your Laravel application.

**This plugin supports the new paypal rest api.**

<a name="paypal-api-credentials"></a>
## PayPal API Credentials

This package uses the new paypal rest api. Refer to this link on how to create API credentials:

https://developer.paypal.com/docs/api/overview/

<a name="installation"></a>
## Installation

* Use following command to install:

If you intend to use ExpressCheckout, please to the following [README](https://github.com/zenithcoder/laravel-paypal/tree/v1.0). *v2.0* & *v3.0* uses the new rest api.

```bash
composer require zenithcoder/laravel-paypal
```

```bash
php artisan vendor:publish --provider "zenithcoder\PayPal\PayPalServiceProvider"
```

<a name="configuration"></a>
## Configuration

* After installation, you will need to add your paypal settings. Following is the code you will find in **config/paypal.php**, which you should update accordingly.

```php
return [
    
    'client_id' => env('PAYPAL_CLIENT_ID'), //clientid from dashboard
  
    'client_secret' => env('PAYPAL_CLIENT_SECRET'),//client secret from dashboard
 
    'paymentUrl' => env('PAYPAL_PAYMENT_URL'),//payment url e.g. sandbox= https://api-m.sandbox.paypal.com
];
```

* Add this to `.env.example` and `.env`

```
#PayPal Setting & API Credentials  
PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
PAYPAL_PAYMENT_URL=

```

##General payment flow

Though there are multiple ways to pay an order, most payment gateways expect you to follow the following flow in your checkout process:

###1. The customer is redirected to the payment provider
After the customer has gone through the checkout process and is ready to pay, the customer must be redirected to site of the payment provider.

The redirection is accomplished by submitting a form with some hidden fields. The form must post to the site of the payment provider. The hidden fields minimally specify the amount that must be paid,etc


###2. The customer pays on the site of the payment provider
The customer arrived on the site of the payment provider and gets to choose a payment method. All steps necessary to pay the order are taken care of by the payment provider.

###3. Handle different Event
We will post an event to the webhook URL set for your transaction's domain. If it was a live transaction, we will post to your live webhook url and vice-versa.

```php
use Zenithcoder\Paypal\Paypal;

  public function handlePaypalWebhook()
    {
        $paymentDetails =  $this->PaypalClient()->getPaymentData();

        dd($paymentDetails);
        $event_types = $paymentDetails['event_types'];

          switch ($event_types)
            {
                case "PAYMENT.SALE.COMPLETED":
                    // Handle payment completed
                    break;
                case "BILLING.SUBSCRIPTION.PAYMENT.FAILED":
                    // Handle payment failed
                    break;
                    // Handle other webhooks
                case "BILLING.SUBSCRIPTION.CANCELLED":
                        // Handle subscription cancelled
                    break;
                case "BILLING.SUBSCRIPTION.SUSPENDED":
                        // Handle subscription suspended
                    break;
                        // Handle other webhooks
                default:
                    break;
            }
    }

```

 
```php
Route::post('/pay', [
    'uses' => 'PaymentController@redirectToPaypal',
    'as' => 'pay'
]);
```

```php
Route::get('/paypal/webhook', 'PaymentController@handlePaypalWebhook');
```

A sample form will look like so:

```html
<form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
        <div class="row" style="margin-bottom:40px;">
          <div class="col-md-8 col-md-offset-2">
            <p>
                <div>
                   Robotsea
                    $350
                </div>
            </p>
            <input type="hidden" name="email" value="awonusiolajide@yahoo.com">  
           <input type="hidden" name="orderID" value="345">
            <input type="hidden" name="amount" value="800"> 
            <input type="hidden" name="quantity" value="3">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="metadata" value="{{ json_encode($array = ['key_name' => 'value',]) }}" > {{-- For other necessary things you want to add to your payload. it is optional though --}}
            
            {{ csrf_field() }} {{-- works only when using laravel 5.1, 5.2 --}}

             <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- employ this in place of csrf_field only in laravel 5.0 --}}


            <p>
              <button class="btn btn-success btn-lg btn-block" type="submit" value="Pay Now!">
              <i class="fa fa-plus-circle fa-lg"></i> Pay Now!
              </button>
            </p>
          </div>
        </div>
</form>
```

## Support

This version supports Laravel 6 or greater.
* In case of any issues, kindly create one on the [Issues](https://github.com/zenithcoder/laravel-paypal/issues) section.
* If you would like to contribute:
  * Fork this repository.
  * Implement your features.
  * Generate pull request.
 
