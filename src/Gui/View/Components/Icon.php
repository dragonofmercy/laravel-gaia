<?php
namespace Gui\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public function __construct(
        public string $icon,
        public string $weight = 'fas'
    ){}

    public function render()
    {
        return gui_fa($this->icon, $this->weight);
    }
}