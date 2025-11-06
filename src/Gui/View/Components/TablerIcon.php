<?php

namespace Gui\View\Components;

use Demeter\Support\Str;
use Illuminate\View\Component;

class TablerIcon extends Component
{
    protected static ?array $cache = null;

    public function __construct(
        public string $name,
        public bool $filled = false,
        public string $class = ""
    ){}

    public function render(): string
    {
        $cache = static::getCache();
        $type = $this->filled ? 'filled' : 'outline';
        $svg = $cache[$this->name]['svg'][$type] ?? $cache['exclamation-circle']['svg'][$type];
        $class = Str::join('icon', $this->class);

        return Str::replace('class="icon"', 'class="' . $class . '"', $svg);
    }

    /**
     * Reloads the cache by reading a JSON file from the specified path and decoding its content.
     *
     * @return void
     */
    protected static function reloadCache(): void
    {
        $jsonPath = __DIR__ . '/../../resources/icons/tabler.json';
        $jsonContent = file_get_contents($jsonPath);
        self::$cache = json_decode($jsonContent, true);
    }

    /**
     * Retrieves the cache, loading it if not already initialized.
     *
     * @return array The current cache data.
     */
    public static function getCache(): array
    {
        if(null === self::$cache){
            static::reloadCache();
        }

        return self::$cache;
    }
}