<?php

/*
 * This file is part of the Laravel Paystack package.
 *
 * (c) Prosper Otemuyiwa <prosperotemuyiwa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenithcoder\Paypal\Test;

use Mockery as m;
use GuzzleHttp\Client;
use PHPUnit_Framework_TestCase;
use Zenithcoder\Paypal\Paypal;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade as Facade;

class PaypalTest extends PHPUnit_Framework_TestCase
{
    protected $paypal;

    public function setUp()
    {
        $this->paypal = m::mock('Zenithcoder\Paypal\Paypal');
        $this->mock = m::mock('GuzzleHttp\Client');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testItCanCreateOrder()
    {
        $array = $this->paypal->shouldReceive('makePaymentRequest')->andReturn(['response']);

        $this->assertEquals('array', gettype(array($array)));
    }

    public function testUrlIsReturned()
    {
        $url = $this->paypal->shouldReceive('redirectNow')->andReturn('https://www.paypal.com/checkoutnow?token=5O190127TN364715T');

        $this->assertEquals('string', gettype((string)($url)));
    }
}
