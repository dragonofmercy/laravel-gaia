<?php
namespace Gui\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getCountries(string|null $locale = null)
 * @method static string getCountry(string $isoCode, string|null $locale = null)
 */
class Countries extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Gui\Translation\Countries::class;
    }
}