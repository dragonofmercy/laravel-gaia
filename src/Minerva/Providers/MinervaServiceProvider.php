<?php

namespace Minerva\Providers;

class MinervaServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();

        $this->app->singleton('exchange.rates', \Minerva\Services\ExchangeRates\Exchange::class);
        $this->app->bind(
            \Minerva\Contracts\ExchangeRateProvider::class,
            fn(\Illuminate\Contracts\Foundation\Application $app) => (new \Minerva\Services\ExchangeRates\ExchangeRatesManager($app))->driver()
        );
    }

    public function boot(): void
    {
        if(app()->runningInConsole()){
            $this->publishConfig();
        }
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/minerva.php', 'minerva');
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/minerva.php' => config_path('minerva.php')
        ], 'minerva-config');
    }
}