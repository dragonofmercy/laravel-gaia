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
        return static::renderIcon($this->name, $this->filled ? 'filled' : 'outline', $this->class);
    }

    /**
     * Reloads the cache by reading a JSON file from the specified path and decoding its content.
     *
     * @return void
     */
    protected static function reloadCache(): void
    {
        $jsonPath = __DIR__ . '/../../resources/icons/';
        self::$cache = [
            'outline' => json_decode(file_get_contents($jsonPath . 'tabler-nodes-outline.json'), true),
            'filled' => json_decode(file_get_contents($jsonPath . 'tabler-nodes-filled.json'), true)
        ];
    }

    /**
     * Renders an SVG icon based on given parameters.
     *
     * @param string $name The name of the icon to render.
     * @param string $type The type of the icon (e.g., 'outline' or 'filled'). Defaults to 'outline'.
     * @param string $class Additional CSS classes to apply to the icon. Defaults to an empty string.
     * @return string The generated SVG icon as a string.
     */
    public static function renderIcon(string $name, string $type = 'outline', string $class = ""): string
    {
        $cache = static::getCache();

        if(!array_key_exists($name, $cache[$type])){
            $name = 'exclamation-circle';
        }

        $iconContent = "";

        foreach($cache[$type][$name] as $part){
            $iconContent.= tag($part[0], $part[1]);
        }

        $attr = [
            'xmlns' => 'http://www.w3.org/2000/svg',
            'width' => '24',
            'height' => '24',
            'viewBox' => '0 0 24 24',
            'fill' => $type === 'filled' ? 'currentColor' : 'none',
            'stroke-linecap' => 'round',
            'stroke-linejoin' => 'round',
            'class' => Str::join('icon', $class)
        ];

        if($type === 'outline'){
            $attr['stroke'] = 'currentColor';
        }

        return content_tag('svg', $iconContent, $attr);
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