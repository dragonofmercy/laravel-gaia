<?php
namespace Gui\Translation;

class Countries extends TranslationFileLoader
{
    protected function getConfigName(): string
    {
        return "countries";
    }

    /**
     * Get all countries
     *
     * @param string|null $locale
     * @return array
     */
    public function getCountries(string|null $locale = null): array
    {
        return $this->getAll($locale);
    }

    /**
     * Get country from isoCode
     *
     * @param string $isoCode
     * @param string|null $locale
     * @return string
     */
    public function getCountry(string $isoCode, string|null $locale = null): string
    {
        return $this->get($isoCode, $locale);
    }
}