<?php
namespace Gui\View\Components;

use Demeter\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $label = "",
        public bool $button = true,
        public string $url = '#',
        public string $icon = '',
        public bool $iconResponsive = false,
        public string $class = '',
        public string $dataModalClass = 'modal-md',
        public array|Collection $attr = []
    ){}

    public function render()
    {
        $attributes = $this->attr;
        $attributes['data-modal-class'] = $this->dataModalClass;
        $attributes['class'] = $this->class;

        if($this->button){
            return gui_button_modal($this->label, $this->url, $this->icon, $attributes);
        } else {
            if(strlen($this->icon)){
                $this->label = _gui_label_icon($this->label, $this->icon, $this->iconResponsive);
                $attributes['class'] = Str::join($attributes['class'], 'icon-flex');
            }

            return gui_modal($this->label, $this->url, $attributes);
        }
    }
}