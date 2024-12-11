<?php
namespace Gui\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class TagFormRemote extends Component
{
    public function __construct(
        public string $name,
        public string $url,
        public string $update,
        public string $method = 'post',
        public array|Collection $options = [],
        public array|Collection $attr = [],
    ){}

    public function render()
    {
        $options = $this->options;
        $options['url'] = $this->url;
        $options['update'] = $this->update;
        $options['method'] = $this->method;

        return form_remote($this->name, $options, $this->attr);
    }
}