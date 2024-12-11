<?php

use Demeter\Facades\Flash as FlashFacade;
use Demeter\Support\Str;
use Illuminate\Support\Collection;

if(!function_exists('_gui_label_icon')){
    /**
     * Get label with icon
     *
     * @param string $label
     * @param string $icon
     * @param bool $responsive
     * @return string
     */
    function _gui_label_icon(string $label, string $icon, bool $responsive = false): string
    {
        return content_tag('i', '', ['class' => $icon]) . ($responsive ? content_tag('span', trans($label), ['class' => 'd-none d-md-block']) : trans($label));
    }
}

if(!function_exists('_gui_button_attributes')){
    /**
     * Get attributes for button
     *
     * @param array|Collection $attributes
     * @return Collection
     */
    function _gui_button_attributes(array|Collection $attributes = []): Collection
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        $classes = explode(' ', $attributes->get('class', ''));

        if(!preg_grep('/^btn-(?!lg|sm)/i', $classes)){
            $classes[] = 'btn-default';
        }

        if(!preg_grep('/^btn$/i', $classes)){
            $classes[] = 'btn';
        }

        if(!preg_grep('/^btn-icon+$/i', $classes)){
            $classes[] = 'btn-icon';
        }

        $attributes['class'] = Str::join('', $classes);

        return $attributes;
    }
}

if(!function_exists('_gui_modal_attributes')){
    /**
     * Get modal attributes
     *
     * @param string $target
     * @param array|Collection $attributes
     * @return Collection
     */
    function _gui_modal_attributes(string $target, array|Collection $attributes = []): Collection
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        $default_attributes = [
            'data-gui-behavior' => 'modal',
            'data-modal-target' => '#gui_modal'
        ];

        if(!$attributes->has('data-modal-class')){
            $attributes['data-modal-class'] = 'modal-md';
        }

        if(str_starts_with($target, '#')){
            $attributes['data-modal-target'] = $target;
        } else {
            $attributes['data-modal-url'] = url($target);
        }

        return collect($default_attributes)->merge($attributes);
    }
}

if(!function_exists('gui_paginator_pages_range')){
    /**
     * Get paginator pages
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginatior
     * @param int $nbPages
     * @return array
     */
    function gui_paginator_pages_range(\Illuminate\Pagination\LengthAwarePaginator $paginatior, int $nbPages = 5): array
    {
        if($nbPages < 3){
            throw new \InvalidArgumentException('nbPages cannot be lower than 3');
        }

        if($nbPages > $paginatior->lastPage()){
            $nbPages = $paginatior->lastPage();
        }

        $ret = [];
        $median = $paginatior->currentPage() - floor($nbPages / 2);
        $firstOfRange = $paginatior->lastPage() - $nbPages + 1;
        $limit = $firstOfRange > 0 ? $firstOfRange : 1;
        $begin = $median > 0 ? (min($median, $limit)) : 1;

        $i = (int) $begin;

        while($i < $begin + $nbPages && $i <= $paginatior->lastPage()){
            $ret[] = $i++;
        }

        return $ret;
    }
}

if(!function_exists('gui_modal')){
    /**
     * Link to modal
     *
     * @param string $label
     * @param string $target
     * @param array|Collection $attributes
     * @return string
     */
    function gui_modal(string $label, string $target = '#', array|Collection $attributes = new Collection()): string
    {
        return l($label, '', _gui_modal_attributes($target, $attributes));
    }
}

if(!function_exists('gui_button_modal')){
    /**
     * Gets button link with icon
     *
     * @param string $label
     * @param string $target
     * @param string $icon
     * @param array|Collection $attributes
     * @return string
     */
    function gui_button_modal(string $label, string $target, string $icon, array|Collection $attributes = new Collection()): string
    {
        return gui_modal(_gui_label_icon($label, $icon), $target, _gui_button_attributes($attributes));
    }
}

if(!function_exists('gui_button_link')){
    /**
     * Gets button link with icon
     *
     * @param string $label
     * @param string|null $url
     * @param string $icon
     * @param array|Collection $attributes
     * @return string
     */
    function gui_button_link(string $label, string|null $url, string $icon, array|Collection $attributes = new Collection()): string
    {
        return l(_gui_label_icon($label, $icon), $url, _gui_button_attributes($attributes));
    }
}

if(!function_exists('gui_button')){
    /**
     * Gets button link with icon
     *
     * @param string $label
     * @param string $icon
     * @param array|Collection $attributes
     * @return string
     */
    function gui_button(string $label, string $icon, array|Collection $attributes = new Collection()): string
    {
        return content_tag('button', _gui_label_icon($label, $icon), _gui_button_attributes($attributes));
    }
}

if(!function_exists('gui_button_link_remote')){
    /**
     * Gets button remote link with icon
     *
     * @param string $label
     * @param string $url
     * @param string $update
     * @param string $icon
     * @param array|Collection $attributes
     * @param array|Collection $options
     * @return string
     */
    function gui_button_link_remote(string $label, string $url, string $update, string $icon, array|Collection $attributes = new Collection(), array|Collection $options = new Collection()) : string
    {
        return lr(_gui_label_icon($label, $icon), $url, $update, _gui_button_attributes($attributes), $options);
    }
}

if(!function_exists('gui_button_submit')){
    /**
     * Gets button submit with icon
     *
     * @param string $label
     * @param string $icon
     * @param string|null $action
     * @param array|Collection $attributes
     * @return string
     */
    function gui_button_submit(string $label, string $icon, string|null $action = null, array|Collection $attributes = new Collection()) : string
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        if(!$attributes->has('data-loading-text')){
            $attributes['data-loading-text'] = trans('gui::messages.generic.loading');
        }

        return button_submit(_gui_label_icon($label, $icon), $action, _gui_button_attributes($attributes));
    }
}

if(!function_exists('gui_button_popup')){
    /**
     * Gets button link with icon
     *
     * @param string $label
     * @param string $target
     * @param string $icon
     * @param array|Collection $options
     * @param array|Collection $attributes
     * @return string
     */
    function gui_button_popup(string $label, string $target, string $icon, array|Collection $options = new Collection(), array|Collection $attributes = new Collection()) : string
    {
        return gui_popup(_gui_label_icon($label, $icon), $target, $options, _gui_button_attributes($attributes));
    }
}

if(!function_exists('gui_popup')){
    /**
     * Link to popup
     *
     * @param string $name
     * @param string $uri
     * @param array|Collection $options
     * @param array|Collection $attributes
     * @return string
     */
    function gui_popup(string $name, string $uri = '#', array|Collection $options = new Collection(), array|Collection $attributes = new Collection()) : string
    {
        if(!$options instanceof Collection){
            $options = collect($options);
        }

        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        $popup_default = collect([
            'toolbar' => 'no',
            'resizable' => 'yes',
            'scrollbars' => 'yes',
            'menubar' => 'no',
            'location' => 'no',
            'directories' => 'no',
            'status' => 'no'
        ]);

        $popup = $popup_default->merge($options);

        $p_name = $popup->get('name', 'gui-popup-window');
        $p_width = $popup->get('width', 750);
        $p_height = $popup->get('height', 300);

        $popup->forget(['name', 'width', 'height']);

        $config = [];

        foreach($popup as $key => $value){
            $config[] = $key . '=' . $value;
        }

        $attributes['onclick'] = $attributes->get('onclick') . "gui.openPopup('" . url($uri) . "', '" . $p_name . "', " . $p_width . ", " . $p_height . ", '" . implode(',', $config) . "'); return false;";

        return l($name, '#', $attributes);
    }
}

if(!function_exists('gui_fa')){
    /**
     * Get font icon
     *
     * @param string $icon
     * @param string $weight
     * @return string
     */
    function gui_fa(string $icon, string $weight = 'fa-solid') : string
    {
        return gui_icon($weight . ' ' . $icon);
    }
}

if(!function_exists('gui_icon')){
    /**
     * Get font icon
     *
     * @param string $icon
     * @return string
     */
    function gui_icon(string $icon) : string
    {
        return content_tag('i', '', array('class' => $icon));
    }
}

if(!function_exists('gui_image_base64')){
    /**
     * Load image to base64
     *
     * @param string $filename
     * @return string
     */
    function gui_image_base64(string $filename) : string
    {
        return 'data:' . mime_content_type($filename) . ';base64,' . base64_encode(file_get_contents($filename));
    }
}

if(!function_exists('gui_darkmode')){
    /**
     * Get value of dark mode cookie
     *
     * @return bool
     */
    function gui_darkmode() : bool
    {
        if(config('gui.darkmode_force')){
            return true;
        }

        $value = \Illuminate\Support\Facades\Request::cookie('dark-mode', 'false');
        return $value == 'true';
    }
}

if(!function_exists('gui_render_flash')){
    /**
     * Render flash message
     *
     * @param string $name
     * @param bool $dismissible
     * @param int $autoHide Time in seconds
     * @param string $additionalClasses
     * @return string
     */
    function gui_render_flash(string $name, bool $dismissible = true, int $autoHide = 0, string $additionalClasses = '') : string
    {
        $flashContent = '';

        if(FlashFacade::has($name)){
            $flash = FlashFacade::get($name);
            $flashMessage = content_tag('i', attributes: ['class' => 'icon']) . content_tag('div', trans($flash->message), ['class' => 'alert-content']);
            $flashMessage.= $dismissible ? content_tag('button', attributes: ['type' => 'button', 'class' => 'btn-close', 'data-bs-dismiss' => 'alert']) : "";
            $flashContent = content_tag('div', $flashMessage, ['class' => Str::join('alert-' . $flash->flag->value, ['alert', $additionalClasses])]);
        }

        if($autoHide > 0){
            $flashContent.= javascript_tag('setTimeout(function(){ $("[data-gui-name=' . $name . '] .alert").hide() }, ' . ($autoHide * 1000) . ');');
        }

        return content_tag('div', $flashContent, ['class' => 'gui-alert-container', 'data-gui-name' => $name]);
    }
}

if(!function_exists('gui_datatable_redirect')){
    /**
     * Prepare an array with remote function options for datatable
     *
     * @param string $id
     * @param string $url
     * @return array
     */
    function gui_datatable_redirect(string $id, string $url) : array
    {
        return [
            'update' => '#' . $id,
            'url' => $url,
            'data' => ['dt_u' => $id],
            'method' => \Illuminate\Http\Request::METHOD_POST,
            'loading' => "$('#" . $id . "').html('<div class=\"datatable-loading\" />');",
            'complete' => "gui.init('#" . $id . "');",
        ];
    }
}