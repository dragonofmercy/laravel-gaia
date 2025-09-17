<?php
namespace Gui\View\Components;

use Gui\Abstracts\View\Components\Button as ButtonBase;

class Button extends ButtonBase
{
    public function render()
    {
        return view('gui::components.button');
    }
}