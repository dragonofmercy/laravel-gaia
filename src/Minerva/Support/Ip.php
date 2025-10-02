<?php
namespace Minerva\Support;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Ip
{
    /**
     * Connexion timeout in seconds
     *
     * @var int
     */
    public static int $timeout = 2;

    /**
     * Sends a GET request to retrieve the IPv4 address from an external service.
     *
     * @return string|bool Returns the IPv4 address as a string on success, or false on failure.
     */
    public static function getV4(): string|bool
    {
        try {
            return Http::connectTimeout(self::$timeout)->get('https://api.ipify.org')->body();
        } catch(Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Sends a GET request to retrieve the IPv6 address from an external service.
     *
     * @return string|bool Returns the IPv6 address as a string on success, or false on failure.
     */
    public static function getV6(): string|bool
    {
        try {
            return Http::connectTimeout(self::$timeout)->get('https://api6.ipify.org')->body();
        } catch(Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Retrieves IP information from the freeipapi.com API as a collection.
     *
     * @return \Illuminate\Support\Collection|bool Returns a collection of IP information if successful, or false on failure.
     */
    public static function getIpInfo(): Collection|bool
    {
        try {
            return Http::connectTimeout(self::$timeout)->get('https://freeipapi.com/api/json')->collect();
        } catch(Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Retrieves the Autonomous System Number (ASN) for the specified IP address from an external service.
     *
     * @param string $ip The IP address for which the ASN is to be retrieved.
     * @return int Returns the ASN as an integer on success, or 0 on failure.
     */
    public static function getAsn(string $ip): int
    {
        try {
            $response = Http::connectTimeout(self::$timeout)->get('https://stat.ripe.net/data/network-info/data.json?resource='.urlencode($ip));
            return Arr::first(Arr::get($response, 'data.asns'), default: 0);
        } catch(Exception $e) {
            report($e);
            return 0;
        }
    }
}