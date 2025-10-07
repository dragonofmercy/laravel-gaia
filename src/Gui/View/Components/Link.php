<?php

namespace Gui\View\Components;

use Illuminate\View\Component;

class Link extends Component
{
    public function __construct(
        public string $confirm = '',
    ){}

    public function render()
    {
        return function(array $data){
            $attributes = $data['attributes'];

            if($this->confirm !== ''){
                $attributes['onClick'] = "return confirm('" . __($this->confirm) . "');";
            }

            $slot = $data['slot'];
            return view('gui::components.link', compact('attributes', 'slot'))->render();
        };
    }
}