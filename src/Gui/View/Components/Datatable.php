<?php

namespace Gui\View\Components;

use Demeter\Support\Str;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

class Datatable extends Component
{
    public function __construct(
        public string $id,
        public string $url,
        public string $breakpoint = 'lg',
        public string $class = ''
    ){}

    public function render()
    {
        return $this->prepare("GuiDatatable.browse('" . $this->url . "', $('#" . $this->id . "'))");
    }

    /**
     * Prepares a JavaScript string for inclusion in an HTML script tag.
     *
     * @param string $action The JavaScript code to be wrapped and prepared.
     * @return string A complete script tag containing the prepared JavaScript code.
     */
    protected function prepare($action): string
    {
        if(!Request::isXmlHttpRequest()){
            $action = "window.addEventListener('DOMContentLoaded',function(){" . $action . "});";
        }

        $attributes = [
            'id' => $this->id,
            'class' => Str::join('datatable', ['datatable-mobile-' . $this->breakpoint, $this->class])
        ];

        return '<div ' . (new ComponentAttributeBag($attributes))->toHtml() . '><div class="loading-container"></div><script>' . $action . '</script></div>';
    }
}