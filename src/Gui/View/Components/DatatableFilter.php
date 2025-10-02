<?php

namespace Gui\View\Components;

use Closure;
use Illuminate\View\Component;

class DatatableFilter extends Component
{
    public function __construct(
        public string $id
    ){}

    public function render(): Closure
    {
        return function(array $data){
            $attributes = $data['attributes'];
            $attributes['href'] = "#datagrid_search_" . $this->id;
            $attributes['data-bs-toggle'] = "collapse";
            $attributes['onclick'] = "this.blur()";

            $slot = $data['slot'];
            return view('gui::components.link', compact('attributes', 'slot'))->render();
        };
    }
}