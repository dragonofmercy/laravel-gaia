<?php
namespace Minerva\Services\ExchangeRates\Providers;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Minerva\Contracts\Services\ExchangeRateProvider;
use Rct567\DomQuery\DomQuery;

readonly class XratesProvider implements ExchangeRateProvider
{
    public function __construct(
        private Factory $client,
        private string  $baseUrl = 'https://www.x-rates.com/calculator'
    ){}

    public function getRates(string $fromCurrency, string $toCurrency, float $amount = 1.0) : float
    {
        return $this->makeRequest($fromCurrency, $toCurrency, $amount);
    }

    private function makeRequest(string $fromCurrency, string $toCurrency, float $amount) : float
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

        return floatval(preg_match('/^[0-9.]+/', (new DomQuery($html))->find('.ccOutputRslt')->text(), $matches) ? $matches[0] : 1);
    }

    private function client() : Factory|PendingRequest
    {
        return $this->client
            ->baseUrl($this->baseUrl);
    }
}