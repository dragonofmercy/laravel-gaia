<?php
namespace Minerva\Providers;

class ExchangeRatesServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot() : void
    {
        $source = realpath(__DIR__ . '/../../../config/minerva.php');
        $this->mergeConfigFrom($source, 'minerva');
        $this->publishes([$source => $this->app->configPath('minerva.php')]);
    }

    public function register() : void
    {
        $this->app->singleton('exchange.rates', \Minerva\Services\ExchangeRates\Exchange::class);
        $this->app->bind(
            \Minerva\Contracts\Services\ExchangeRateProvider::class,
            fn(\Illuminate\Contracts\Foundation\Application $app) => (new \Minerva\Services\ExchangeRates\ExchangeRatesManager($app))->driver()
        );
    }
}