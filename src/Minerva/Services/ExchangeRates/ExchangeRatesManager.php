<?php

namespace Minerva\Services\ExchangeRates;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Manager;
use Minerva\Services\ExchangeRates\Providers\CachedProvider;
use Minerva\Services\ExchangeRates\Providers\FrankfurterProvider;
use Minerva\Services\ExchangeRates\Providers\XratesProvider;

class ExchangeRatesManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return strval($this->config->get('minerva.rates.driver') ?? 'frankfurter');
    }

    /**
     * Create an instance of the Frankfurter driver.
     *
     * @return FrankfurterProvider
     */
    public function createFrankfurterDriver(): FrankfurterProvider
    {
        return new FrankfurterProvider($this->container->make(Factory::class));
    }

    /**
     * Create an instance of the Xrates driver.
     *
     * @return XratesProvider
     */
    public function createXratesDriver(): XratesProvider
    {
        return new XratesProvider($this->container->make(Factory::class));
    }

    /**
     * Create an instance of the Cached driver.
     *
     * @return CachedProvider
     */
    public function createCachedDriver(): CachedProvider
    {
        $factory = $this->container->make(CacheFactory::class);

        return new CachedProvider(
            $factory->store($this->config->get('minerva.rates.cache.store')),
            $this->driver($this->config->get('minerva.rates.cache.driver', 'frankfurter')),
            strval($this->config->get('minerva.rates.cache.key', 'cached_exchange_rates')),
            intval($this->config->get('minerva.rates.cache.ttl', 900)),
        );
    }
}