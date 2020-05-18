<?php
namespace ShahBurhan\LaravelPayPalServiceProvider
use Illuminate\Support\ServiceProvider;

class LaravelPayPalServiceProvider extends ServiceProvider
{
    /**
    * Publishes configuration file.
    *
    * @return  void
    */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('laravel_paypal.php'),
        ], 'laravel_paypal');
    }

    /**
    * Make config publishment optional by merging the config from the package.
    *
    * @return  void
    */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php',
            'laravel_paypal'
        );
    }
}