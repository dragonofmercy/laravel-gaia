<?php
namespace Gui\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Submit extends Component
{
    public function __construct(
        public string $label = "",
        public ?string $action = null,
        public string $icon = '',
        public string $class = '',
        public bool $iconResponsive = false,
        public bool|string $loading = true,
        public array|Collection $attr = []
    ){}

    public function render()
    {
        $attributes = $this->attr;
        $attributes['class'] = $this->class;

        if($this->loading !== false){
            $attributes['data-loading-text'] = trans(is_string($this->loading) ? $this->loading : 'gui::messages.generic.loading');
        }

        if(strlen($this->icon)){
            $this->label = _gui_label_icon($this->label, $this->icon, $this->iconResponsive);
            return button_submit($this->label, $this->action, _gui_button_attributes($attributes));
        }

        return button_submit($this->label, $this->action, $attributes);
    }
}