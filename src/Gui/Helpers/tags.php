<?php
use Illuminate\Support\Collection;

if(!function_exists('_tag_options')){
    /**
     * Build attributes string for html tags
     *
     * @param array|Collection $options
     * @return string
     * @internal
     */
    function _tag_options(array|Collection $options = new Collection()): string
    {
        $output = '';
        $escaper = new \Laminas\Escaper\Escaper();

        foreach($options as $key => $value){
            if(null === $value){
                $output .= ' ' . $escaper->escapeHtmlAttr($key);
            } else {
                $output .= ' ' . $escaper->escapeHtmlAttr($key) . '="' . $escaper->escapeHtml($value) . '"';
            }
        }

        return $output;
    }
}

if(!function_exists('_options_to_attributes')){
    /**
     * Convert options to attributes (for links)
     *
     * @param array|Collection $options
     * @return Collection
     */
    function _options_to_attributes(array|Collection $options): Collection
    {
        if(!$options instanceof Collection){
            $options = collect($options);
        }

        $confirm = $options->get('confirm', false);
        $popup = $options->get('popup', false);
        $onClick = $options->get('onclick', false);

        $options->forget('confirm');
        $options->forget('popup');

        if($confirm && $popup){
            $options['onclick'] = "if(" . _javscript_confirm($confirm) . "){" . $onClick . _javscript_popup($popup) . "};return false;";
        } elseif($confirm) {
            if($onClick){
                $options['onclick'] = 'if(' . _javscript_confirm($confirm) . '){return ' . $onClick . '}else{return false;}';
            } else {
                $options['onclick'] = 'return ' . _javscript_confirm($confirm) . ';';
            }
        } elseif($popup) {
            $options['onclick'] = $onClick . _javscript_popup($popup) . 'return false;';
        }

        return $options;
    }
}

if(!function_exists('_javscript_confirm')){
    /**
     * Get javascript confirm code
     *
     * @param string $message
     * @return string
     */
    function _javscript_confirm(string $message): string
    {
        return "confirm('" . _javscript_escape(trans($message)) . "')";
    }
}

if(!function_exists('_javscript_popup')){
    /**
     * Get javascript popup code
     *
     * @param array|Collection|bool $options
     * @return string
     */
    function _javscript_popup(array|Collection|bool $options): string
    {
        if(is_bool($options)){
            $options = [];
        }

        if(!$options instanceof Collection){
            $options = collect($options);
        }

        $default = [
            'toolbar' => 'no', 'resizable' => 'yes', 'scrollbars' => 'yes',
            'menubar' => 'no', 'location' => 'no', 'directories' => 'no',
            'status' => 'no'
        ];

        $options = collect($default)->merge($options)->map(function($value, $key){
            return "$key=$value";
        });

        return "let w=window.open(this.href,'popup','config=" . implode(',', $options) . "');w.focus();";
    }
}

if(!function_exists('_javscript_escape')){
    /**
     * Escape javascript
     *
     * @param $input
     * @return string
     */
    function _javscript_escape($input): string
    {
        return (new \Laminas\Escaper\Escaper)->escapeJs($input);
    }
}

if(!function_exists('_javascript_php_to_object')){
    /**
     * Convert array to object for javascript
     *
     * @param Collection|array $haystack
     * @return string
     */
    function _javascript_php_to_object(Collection|array $haystack): string
    {
        if($haystack instanceof Collection){
            $haystack = $haystack->toArray();
        }

        $opts = [];

        foreach($haystack as $key => $value){
            if(is_array($value)){
                $value = _javascript_php_to_object($value);
            }
            $opts[] = "'" . _javscript_escape($key) . "':" . _javascript_var_to_string($value);
        }

        return "{" . implode(',', $opts) . "}";
    }
}

if(!function_exists('_javascript_php_to_array')){
    /**
     * Convert array to object for javascript
     *
     * @param Collection|array $haystack
     * @return string
     */
    function _javascript_php_to_array(Collection|array $haystack): string
    {
        if($haystack instanceof Collection){
            $haystack = $haystack->toArray();
        }

        return "[" . implode(',', $haystack) . "]";
    }
}

if(!function_exists('_javascript_var_to_string')){
    /**
     * Convert a variable to javascript string var
     *
     * @param string $value
     * @return string
     */
    function _javascript_var_to_string(mixed $value): string
    {
        if(preg_match('/^\{.*}$/', (string) $value) || preg_match('/^\[.*]$/', (string) $value) || \Demeter\Support\Str::startsWith((string) $value, ['function(', '$('])){
            return (string) $value;
        }

        if(str_starts_with((string) $value, '|')){
            $value = substr((string) $value, 1);
        } elseif(is_bool($value)) {
            $value = $value ? "true" : "false";
        } elseif(null === $value) {
            $value = "null";
        } elseif(is_string($value)) {
            $value = "'" . _javscript_escape($value) . "'";
        }

        return (string) $value;
    }
}

if(!function_exists('tag')){
    /**
     * Render html tag
     *
     * @param string $name
     * @param array|Collection $attributes
     * @param bool $isOpen
     * @return string
     */
    function tag(string $name, array|Collection $attributes = new Collection(), bool $isOpen = false): string
    {
        return '<' . $name . _tag_options($attributes) . (($isOpen) ? '>' : ' />');
    }
}

if(!function_exists('content_tag')){
    /**
     * Render html content tag
     *
     * @param string $name
     * @param string|null $content
     * @param array|Collection $attributes
     * @return string
     */
    function content_tag(string $name, string|null $content = null, array|Collection $attributes = new Collection()): string
    {
        return '<' . $name . _tag_options($attributes) . '>' . $content . '</' . $name . '>';
    }
}

if(!function_exists('tag_image')){
    /**
     * Create an img tag
     *
     * @param string $src
     * @param array|Collection $attributes
     * @return string
     */
    function tag_image(string $src, array|Collection $attributes = new Collection()): string
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        $attributes['src'] = $src;

        if(!$attributes->has('alt'))
        {
            $attributes['alt'] = substr(basename($src), 0, strpos(basename($src), '.'));
        }

        return tag('img', $attributes);
    }
}

if(!function_exists('tag_form')){
    /**
     * Create a form tag
     *
     * @param string|null $url
     * @param array|Collection $attributes
     * @param boolean $multipart
     * @return string
     */
    function tag_form(string|null $url = null, array|Collection $attributes = new Collection(), bool $multipart = false): string
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        if(null === $url){
            $url = $_SERVER['REQUEST_URI'];
        }

        $attributes['action'] = $attributes['action'] ?? url($url);
        $attributes['method'] = $attributes['method'] ?? 'post';

        if($attributes->get('novalidate', true)){
            $attributes['novalidate'] = 'novalidate';
        }

        if($multipart){
            $attributes['enctype'] = $attributes['enctype'] ?? 'multipart/form-data';
        }

        return tag('form', $attributes, true);
    }
}

if(!function_exists('form_remote')){
    /**
     * Get form remote tag
     *
     * @param string $name
     * @param array|Collection $options
     * @param array|Collection $attributes
     * @return string
     */
    function form_remote(string $name, array|Collection $options, array|Collection $attributes = new Collection()): string
    {
        if(!$options instanceof Collection){
            $options = collect($options);
        }

        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        $options['form'] = true;
        $options['method'] = $options->get('method', 'post');

        $attributes['name'] = $name;
        $attributes['method'] = $options->get('method');
        $attributes['onsubmit'] = remote_function($options) . ' return false;';
        $attributes['action'] = url($attributes->get('action', $options['url']));

        if($attributes->get('novalidate', true)){
            $attributes['novalidate'] = 'novalidate';
        }

        return '<form' . _tag_options($attributes) . '>';
    }
}

if(!function_exists('button_submit')){
    /**
     * Generate a submit with a link or button
     *
     * @param string $content
     * @param string|null $formName
     * @param array|Collection $attributes
     * @return string
     */
    function button_submit(string $content, string|null $formName = null, array|Collection $attributes = new Collection()): string
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        if(null !== $formName){
            $default_attributes = collect(['href' => '#', 'onclick' => 'document.' . $formName . '.submit(); return false;', 'onfocus' => 'this.blur();']);
            $attributes = $default_attributes->merge($attributes);
            return content_tag('a', trans($content), $attributes);
        } else {
            $attributes = _options_to_attributes($attributes);
            if(!$attributes->has('type')){
                $attributes['type'] = 'submit';
            }
            return content_tag('button', trans($content), $attributes);
        }
    }
}

if(!function_exists('tag_close')){
    /**
     * Create a close tag
     *
     * @param string $name
     * @return string
     */
    function tag_close(string $name): string
    {
        return '</' . $name . '>';
    }
}