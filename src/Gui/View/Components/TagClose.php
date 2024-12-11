<?php
namespace Gui\View\Components;

use Illuminate\View\Component;

class TagClose extends Component
{
    public function __construct(
        public string $name = 'form'
    ){}

    public function render()
    {
        return tag_close($this->name);
    }
}