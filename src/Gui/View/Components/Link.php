<?php
namespace Gui\View\Components;

use Demeter\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Link extends Component
{
    public function __construct(
        public string $label = "",
        public string $url = '#',
        public bool $button = false,
        public bool|string $loading = false,
        public string $icon = '',
        public string $class = '',
        public ?string $target = null,
        public bool $iconResponsive = false,
        public array|Collection $attr = []
    ){}

    public function render()
    {
        $attributes = $this->attr;
        $attributes['class'] = $this->class;

        if(null !== $this->target){
            $attributes['target'] = $this->target;
        }

        if($this->loading !== false){
            $attributes['data-loading-text'] = trans(is_string($this->loading) ? $this->loading : 'gui::messages.generic.loading');
        }

        if($this->button){
            return gui_button_link($this->label, $this->url, $this->icon, $attributes);
        } else {
            if(strlen($this->icon)){
                $this->label = _gui_label_icon($this->label, $this->icon, $this->iconResponsive);
                $attributes['class'] = Str::join($attributes['class'], 'icon-flex');
            }

            return l($this->label, $this->url, $attributes);
        }
    }
}