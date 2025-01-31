<?php
namespace Minerva\Contracts\Services;

interface ExchangeRateProvider
{
    /**
     * Get rates
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param float $amount
     * @return float
     */
    public function getRates(string $fromCurrency, string $toCurrency, float $amount = 1.0): float;
}