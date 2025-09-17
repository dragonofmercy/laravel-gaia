<?php
namespace Minerva\Services\ExchangeRates\Providers;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Minerva\Contracts\ExchangeRateProvider;

readonly class XratesProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string  $baseUrl = 'https://www.x-rates.com/calculator'
    ){}

    public function getRates(string $fromCurrency, string $toCurrency, float $amount = 1.0): float
    {
        return $this->makeRequest($fromCurrency, $toCurrency, $amount);
    }

    private function makeRequest(string $fromCurrency, string $toCurrency, float $amount): float
    {
        $html = $this->client()
            ->get('', [
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'amount' => strval($amount)
            ])
            ->throw()
            ->getBody()
            ->getContents();

        if(preg_match('/class="ccOutputRslt">([0-9]+(?:\.[0-9]+)?)</', $html, $matches)){
            return floatval($matches[1]);
        }

        return 1.0;
    }

    private function client(): Factory|PendingRequest
    {
        return $this->client
            ->baseUrl($this->baseUrl);
    }
}