<?php
use Illuminate\Support\Collection;

if(!function_exists('l')){
    /**
     * Get link tag
     *
     * @param string $name
     * @param string|null $url
     * @param array|Collection $options
     * @return string
     */
    function l(string $name, string|null $url = '#', array|Collection $options = new Collection()): string
    {
        if(!$options instanceof Collection){
            $options = collect($options);
        }

        if(strlen($name) === 0 && empty($url)){
            return "";
        }

        $attributes = _options_to_attributes($options);

        if(!empty($url)){
            $attributes['href'] = url($url);
        } elseif($url === '') {
            $attributes['href'] = "";
        }

        $name = strlen($name) === 0 ? $attributes['href'] : trans($name);

        return content_tag('a', $name, $attributes);
    }
}

if(!function_exists('lr')){
    /**
     * Link to remote tag
     *
     * @param string $name
     * @param string $url
     * @param string $update
     * @param array|Collection $attributes
     * @param array|Collection $options
     * @return string
     */
    function lr(string $name, string $url, string $update, array|Collection $attributes = new Collection(), array|Collection $options = new Collection()): string
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        if(!$options instanceof Collection){
            $options = collect($options);
        }

        $attributes['href'] = url($url);
        $remote = remote_function(collect(['url' => $url, 'update' => $update])->merge($options)) . ' return false';

        if($attributes->has('confirm')){
            $message = _javscript_escape($attributes->get('confirm'));
            $attributes->forget('confirm');
            $remote = "if(window.confirm('$message')){ $remote }else{ return false; }";
        }

        $attributes['onclick'] = $remote;
        return content_tag('a', trans($name), $attributes);
    }
}