<?php

namespace Minerva\Services\ExchangeRates;

use Minerva\Contracts\ExchangeRateProvider;

readonly class Exchange
{
    public function __construct(
        private ExchangeRateProvider $provider
    ){}

    /**
     * Get rate
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param float $amount
     * @return float
     */
    public function get(string $fromCurrency, string $toCurrency, float $amount = 1.0): float
    {
        return $this->provider->getRates($fromCurrency, $toCurrency, $amount);
    }
}