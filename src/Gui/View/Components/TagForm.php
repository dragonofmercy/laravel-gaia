<?php
namespace Gui\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class TagForm extends Component
{
    public function __construct(
        public ?string $url = null,
        public bool $multipart = false,
        public array|Collection $attr = []
    ){}

    public function render()
    {
        return tag_form($this->url, $this->attr, $this->multipart);
    }
}