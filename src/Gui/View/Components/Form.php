<?php

namespace Gui\View\Components;

use Closure;
use Illuminate\View\Component;

class Form extends Component
{
    public function __construct(
        public bool $multipart = false,
    ){}

    public function render(): Closure
    {
        return function (array $data) {
            $attributes = $data['attributes'];

            if($this->multipart === true){
                $attributes = $attributes->merge([
                    'enctype' => 'multipart/form-data'
                ]);
            }

            $slot = $data['slot'];

            return view('gui::components.form', compact('attributes', 'slot'))->render();
        };
    }
}