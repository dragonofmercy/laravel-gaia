<?php
namespace Gui\View\Components;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class DatatableSelector extends Component
{
    public function __construct(
        public string $id,
        public string $url,
        public string $method = Request::METHOD_POST,
        public string $modalSize = 'modal-md',
        public string|null $confirm = null,
        public string $type = 'remote',
        public string $query = 'ids'
    ){}

    public function render(): Closure
    {
        return function(array $data){
            $attributes = $data['attributes'];
            $attributes['href'] = "";
            $attributes['data-url'] = $this->url;
            $attributes['data-target'] = $this->id;
            $attributes['data-query'] = $this->query;
            $attributes['data-type'] = $this->type;
            $attributes['data-modal-size'] = $this->modalSize;
            $attributes['data-method'] = $this->method;
            $attributes['data-gui-behavior'] = 'datatable-selector';

            if($this->confirm){
                $attributes['data-confirm'] = $this->confirm;
            }

            $slot = $data['slot'];
            return view('gui::components.link', compact('attributes', 'slot'))->render();
        };
    }
}