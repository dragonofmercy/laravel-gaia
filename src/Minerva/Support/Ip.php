<?php
namespace Minerva\Support;

use Exception;
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
}