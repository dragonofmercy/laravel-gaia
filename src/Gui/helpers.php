<?php
if(!function_exists('tabler_icon')){
    /**
     * Renders a Tabler icon component with the specified name and attributes.
     *
     * @param string $name The name of the Tabler icon to render.
     * @param string $class
     * @return string The rendered Tabler icon component as a string.
     */
    function tabler_icon(string $name, string $class = ""): string
    {
        return \Illuminate\Support\Facades\Blade::render('<x-gui::tabler-icon :name="$name" :class="$class" />', ['name' => $name, 'class' => $class]);
    }
}

if(!function_exists('tag')){
    /**
     * Generates an HTML tag with the specified attributes.
     *
     * @param string $tag The name of the HTML tag to generate.
     * @param array $attributes An associative array of attributes to include in the tag.
     * @return string The generated HTML tag as a string.
     */
    function tag(string $tag, array $attributes = []): string
    {
        $attributesBag = new \Illuminate\View\ComponentAttributeBag($attributes);
        return '<' . $tag . ' ' . $attributesBag->toHtml() . ' />';
    }
}

if(!function_exists('content_tag')){
    /**
     * Generates an HTML element with the specified tag, content, and attributes.
     *
     * @param string $tag The HTML tag name to be generated.
     * @param string|null $content The content to be placed inside the HTML element.
     * @param array $attributes An associative array of attributes to be applied to the HTML element.
     * @return string The generated HTML element as a string.
     */
    function content_tag(string $tag, ?string $content, array $attributes = []): string
    {
        $attributesBag = new \Illuminate\View\ComponentAttributeBag($attributes);
        return '<' . $tag . ' ' . $attributesBag->toHtml() . '>' . $content . '</' . $tag . '>';
    }
}