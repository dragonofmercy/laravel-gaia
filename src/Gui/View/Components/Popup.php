<?php
namespace Gui\View\Components;

use Demeter\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Popup extends Component
{
    public function __construct(
        public string $label,
        public bool $button = true,
        public string $url = '#',
        public string $icon = '',
        public bool $iconResponsive = false,
        public string $class = '',
        public bool $toolbar = false,
        public bool $resizable = true,
        public bool $scrollbars = true,
        public bool $menubar = false,
        public bool $location = false,
        public bool $directories = false,
        public bool $status = false,
        public string $name = 'gui-popup-window',
        public int $width = 750,
        public int $height = 300,
        public array|Collection $attr = []
    ){}

    public function render()
    {
        $options = [
            'toolbar' => $this->toolbar ? 'yes' : 'no',
            'resizable' => $this->resizable ? 'yes' : 'no',
            'scrollbars' => $this->scrollbars ? 'yes' : 'no',
            'menubar' => $this->menubar ? 'yes' : 'no',
            'location' => $this->location ? 'yes' : 'no',
            'directories' => $this->directories ? 'yes' : 'no',
            'status' => $this->status ? 'yes' : 'no',
            'width' => $this->width,
            'height' => $this->height
        ];

        $attributes = $this->attr;
        $attributes['class'] = $this->class;

        if($this->button){
            return gui_button_popup($this->label, $this->url, $this->icon, $options, $attributes);
        } else {
            if(strlen($this->icon)){
                $this->label = _gui_label_icon($this->label, $this->icon, $this->iconResponsive);
                $attributes['class'] = Str::join($attributes['class'], 'icon-flex');
            }

            return gui_popup($this->label, $this->url, $options, $attributes);
        }
    }
}