<?php

namespace Gui\Abstracts\View\Components;

use Illuminate\View\Component;

abstract class Button extends Component
{
    public static $defaultLoadingStyle = "circle";

    public function __construct(
        public string $type = "button",
        public ?string $loadingStyle = null
    ){
        $this->loadingStyle = $this->loadingStyle ?? static::$defaultLoadingStyle;
    }
}