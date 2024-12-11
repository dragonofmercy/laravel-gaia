<?php
namespace Minerva\Services\ExchangeRates\Providers;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Minerva\Contracts\Services\ExchangeRateProvider;

readonly class FrankfurterProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string  $baseUrl = 'https://api.frankfurter.app'
    ){}

    public function getRates(string $fromCurrency, string $toCurrency, float $amount = 1.0) : float
    {
        $data = $this->makeRequest($fromCurrency, $toCurrency);
        return $data['rates'][$toCurrency] * $amount;
    }

    private function makeRequest(string $fromCurrency, string $toCurrency) : Collection
    {
        return $this->client()
            ->get('/latest', [
                'from' => $fromCurrency,
                'to' => $toCurrency
            ])
            ->throw()
            ->collect();
    }

    private function client() : Factory|PendingRequest
    {
        return $this->client
            ->baseUrl($this->baseUrl)
            ->asJson()
            ->acceptJson();
    }
}