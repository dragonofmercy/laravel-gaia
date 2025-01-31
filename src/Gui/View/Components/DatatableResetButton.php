<?php
namespace Gui\View\Components;

use Illuminate\Http\Request;
use Illuminate\View\Component;

class DatatableResetButton extends Component
{
    public function __construct(
        public string $id,
        public string $url,
        public string $label = 'gui::messages.datatable.reset',
        public string $icon = 'fas fa-heart-circle-bolt',
        public bool   $responsive = true
    ){}

    public function render(): string
    {
        $attributes = [
            'class' => 'icon-flex'
        ];

        $options = [
            'method' => Request::METHOD_POST,
            'data' => ['dt_u' => $this->id, 'dt_c' => 1]
        ];

        return lr(_gui_label_icon($this->label, $this->icon, $this->responsive), $this->url, $this->id, $attributes, $options);
    }
}