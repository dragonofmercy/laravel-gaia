<?php
namespace Gui\View\Components;

use Illuminate\Routing\Controller;
use Illuminate\View\Component;
use Illuminate\View\View;

class Partial extends Component
{
    public function __construct(
        public string|array|null $route = null,
        public array $params = []
    ){}

    public function render() : string|View
    {
        if(is_array($this->route)){
            list($controllerClass, $method) = $this->route;
            /** @var Controller $controller */
            $controller = new $controllerClass;
            return $controller->{$method}(...$this->params)->render();
        } elseif(is_string($this->route)){
            return view($this->route)->with($this->params);
        }

        return "";
    }
}