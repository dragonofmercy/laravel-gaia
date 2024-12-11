<?php
namespace Gui\View\Components;

use Illuminate\View\Component;

class Flash extends Component
{
    public function __construct(
        public string $name = 'notify',
        public bool $dismissible = true,
        public int $autoHide = 0,
        public string $additionalClasses = ''
    ){}

    public function render()
    {
        return gui_render_flash($this->name, $this->dismissible, $this->autoHide, $this->additionalClasses);
    }
}