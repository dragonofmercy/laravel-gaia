<?php

namespace Gui\Translation;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;
use RuntimeException;

class Countries
{
    const PATH = '/vendor/petercoles/multilingual-country-list/data/{locale}.php';

    /**
     * Countries instances
     *
     * @var Collection|null
     */
    protected static Collection|null $instance = null;

    /**
     * List of countries
     *
     * @var Collection
     */
    protected Collection $data;

    /**
     * Class constructor that initializes the object with a locale-specific dataset.
     *
     * @param string $locale The locale string used to determine the file path for loading data.
     * @return void
     *
     * @throws RuntimeException If the specified file does not exist.
     */
    public function __construct(string $locale)
    {
        $path = base_path(Str::replace('{locale}', $locale, self::PATH));

        if(!file_exists($path)){
            throw new RuntimeException("The file $path does not exist.");
        }

        $this->data = collect(require $path);
    }

    /**
     * Retrieves the collection of data.
     *
     * @return Collection A collection of data elements.
     */
    public function getCollection(): Collection
    {
        return $this->data;
    }

    /**
     * Retrieves a subset of elements based on the specified countries and locale.
     *
     * @param Enumerable|array|string|null $countries The countries to filter the elements by. Can be an enumerable, array, string, or null for no filtering.
     * @param string|null $locale The locale to filter the elements by. If null, no locale filtering is applied.
     * @return Collection A collection of filtered elements.
     */
    public static function only(Enumerable|array|string|null $countries = null, string|null $locale = null): Collection
    {
        return self::getInstance($locale)->getCollection()->only($countries);
    }

    /**
     * Retrieves all elements, optionally filtered by a specific locale.
     *
     * @param string|null $locale The locale to filter the elements by. If null, no filtering is applied.
     * @return Collection A collection of elements.
     */
    public static function all(string|null $locale = null): Collection
    {
        return self::only(null, $locale);
    }

    /**
     * Retrieves the name of a country based on its code and an optional locale.
     *
     * @param string $code The country code to retrieve the name for.
     * @param string|null $locale The locale to fetch the country name in. If null, the default locale is used.
     * @return string The name of the country, or the country code if the name is not found.
     */
    public static function getCountryName(string $code, string $locale = null): string
    {
        return self::getInstance($locale)->getCollection()->get($code, $code);
    }

    /**
     * Retrieves a singleton instance of the Countries object for the specified or default locale.
     *
     * @param string|null $locale The locale for which the instance should be retrieved. If null, the default locale is used.
     * @return Countries The singleton instance of the Countries object for the specified locale.
     */
    protected static function getInstance(string|null $locale = null): Countries
    {
        $locale = $locale ?? app()->getLocale();

        if(static::$instance === null){
            static::$instance = new Collection();
        }

        if(static::$instance->get($locale) === null){
            static::$instance->put($locale, new Countries($locale));
        }

        return static::$instance->get($locale);
    }
}