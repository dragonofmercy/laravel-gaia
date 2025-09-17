<?php
namespace Minerva\Services\ExchangeRates\Providers;

use Illuminate\Cache\Repository;
use Minerva\Contracts\ExchangeRateProvider;

readonly class CachedProvider implements ExchangeRateProvider
{
    public function __construct(
        private Repository $cache,
        private ExchangeRateProvider $driver,
        private string $key,
        private int $ttl
    ){}

    public function getRates(string $fromCurrency, string $toCurrency, float $amount = 1.0): float
    {
        $data = $this->cache->remember(
            "$this->key:$fromCurrency:$toCurrency",
            $this->ttl,
            fn() => $this->driver->getRates($fromCurrency, $toCurrency)
        );

        return $data * $amount;
    }
}