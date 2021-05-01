<?php

/*
 * This file is part of the Laravel Paypal package.
 *
 * (c) Awonusi Olajide <awonusiolajide@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenithcoder\Paypal\Facades;

use Illuminate\Support\Facades\Facade;

class Paypal extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-paypal';
    }
}
