<?php
namespace Gui\Translation;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class TranslationFileLoader
{
    /**
     * Data cache
     * @var Collection<string, Collection<string, string>>
     */
    protected Collection $data;

    /**
     * Read lang file defined in gui config
     *
     * @param string $locale
     * @return void
     */
    protected function read(string $locale): void
    {
        if(!isset($this->data)){
            $this->data = new Collection();
        }

        if(!$this->data->has($locale)){
            $path = base_path(Str::replace('{locale}', $locale, config('gui.translations.path.' . $this->getConfigName())));
            if(!file_exists($path)){
                throw new \RuntimeException(ucfirst($this->getConfigName()) . " translation file cannot be loaded for this locale [$locale]");
            }
            $this->data[$locale] = collect(require $path);
        }
    }

    /**
     * Get value from key
     *
     * @param string $key
     * @param string|null $locale
     * @return string
     */
    protected function get(string $key, string|null $locale = null): string
    {
        if(null === $locale){
            $locale = app()->currentLocale();
        }
        $this->read($locale);

        return $this->data->get($locale)->get($key, "");
    }

    /**
     * Get all values
     *
     * @param string|null $locale
     * @return array
     */
    protected function getAll(string|null $locale = null): array
    {
        if(null === $locale){
            $locale = app()->currentLocale();
        }
        $this->read($locale);

        return $this->data->get($locale)->all();
    }

    /**
     * Get config name
     *
     * @return string
     */
    abstract protected function getConfigName(): string;
}