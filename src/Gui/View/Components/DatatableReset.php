<?php
namespace Gui\View\Components;

use Closure;
use Illuminate\View\Component;

class DatatableReset extends Component
{
    public function __construct(
        public string $id,
        public string $url
    ){}

    public function render(): Closure
    {
        return function(array $data){
            $attributes = $data['attributes'];
            $attributes['href'] = "";
            $attributes['data-url'] = $this->url;
            $attributes['data-target'] = $this->id;
            $attributes['data-gui-behavior'] = 'datatable-reset';

            $slot = $data['slot'];
            return view('gui::components.link', compact('attributes', 'slot'))->render();
        };
    }
}