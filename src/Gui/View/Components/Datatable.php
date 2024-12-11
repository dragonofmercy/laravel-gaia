<?php
namespace Gui\View\Components;

use Demeter\Support\Str;
use Illuminate\View\Component;

class Datatable extends Component
{
    public function __construct(
        public string $id,
        public string $url,
        public string $breakpoint = 'lg',
        public string $class = ''
    ){}

    public function render() : string
    {
        $remote = remote_function(gui_datatable_redirect($this->id, $this->url));
        return content_tag('div', javascript_tag_deferred($remote), ['id' => $this->id, 'class' => Str::join('gui-datatable', ['datatable-expand-' . $this->breakpoint, $this->class])]);
    }
}