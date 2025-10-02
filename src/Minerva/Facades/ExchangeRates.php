<?php

namespace Minerva\Facades;

/**
 * @method static float get(string $fromCurrency, string $toCurrency, float $amount = 1.0)
 */
class ExchangeRates extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'exchange.rates';
    }
}