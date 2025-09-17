<?php
namespace Gui\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class ThemeToggle extends Component
{
    public function __construct(
        public string $title = "Nox|Lumos",
    )
    {
        if(!Str::contains($this->title, '|')){
            throw new \InvalidArgumentException('Title must contain a pipe character!');
        }
    }

    public function render(): View|string
    {
        return view('gui::components.theme-toggle');
    }
}
