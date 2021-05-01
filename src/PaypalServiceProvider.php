<?php

namespace Zenithcoder\Paypal;

use Illuminate\Support\ServiceProvider;


class PaypalServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../resources/config/paypal.php' => config_path('paypal.php')
		]);
	}

 	public function register()
	{
       
 	$this->app->singleton(Paypal::class, function() {
			return new Paypal();
		});
	}  

     /**
    * Register the application services.
    */
  /*  public function register()
    {
        $this->app->bind('laravel-paypal', function () {

            return new Paypal;

        });
    }*/

    /**
    * Get the services provided by the provider
    * @return array
    */
    public function provides()
    {
        return ['laravel-paypal'];
    }
}
