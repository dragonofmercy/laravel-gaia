<?php
namespace Minerva\Providers;

class ExchangeRatesServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register() : void
    {
        $this->app->singleton('exchange.rates', \Minerva\Services\ExchangeRates\Exchange::class);
        $this->app->bind(
            \Minerva\Contracts\Services\ExchangeRateProvider::class,
            fn(\Illuminate\Contracts\Foundation\Application $app) => (new \Minerva\Services\ExchangeRates\ExchangeRatesManager($app))->driver()
        );
    }
}