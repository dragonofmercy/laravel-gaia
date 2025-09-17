<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Exchange rate configuration
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage the source of the exchange rates informations.
    |
    | Supported drivers: "cached", "frankfurter", "xrates"
    |
    */

    'rates' => [
        'driver' => env('RATES_CACHE_STORE', 'cached'),
        'cache' => [
            'store' => env('RATES_CACHE_STORE'),
            'driver' => env('RATES_CACHE_DRIVER', 'frankfurter'),
            'key' => env('RATES_CACHE_KEY', 'exchange_rates'),
            'ttl' => env('RATES_CACHE_TTL', 900),
        ]
    ]

];