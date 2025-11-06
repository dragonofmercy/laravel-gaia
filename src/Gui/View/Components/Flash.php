<?php

namespace Gui\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use Illuminate\View\View;
use InvalidArgumentException;

class Flash extends Component
{
    const TYPE_SUCCESS = "success";
    const TYPE_DANGER = "danger";
    const TYPE_WARNING = "warning";
    const TYPE_INFO = "info";
    const VALID_TYPES = [
        self::TYPE_SUCCESS,
        self::TYPE_DANGER,
        self::TYPE_WARNING,
        self::TYPE_INFO
    ];

    protected bool $hasFlash = true;
    protected string $type = self::TYPE_SUCCESS;
    protected string $message;

    public function __construct(
        public string $name = 'notify',
        public bool $toast = false,
        public bool $dismissible = true,
    ){
        $flash = session()->get($this->name);

        if(null === $flash){
            $this->hasFlash = false;
            return;
        }

        if(is_array($flash) && isset($flash['type']) && isset($flash['message'])){
            $this->type = $flash['type'];
            $this->message = $flash['message'];
        } else {
            $this->type = self::TYPE_SUCCESS;
            $this->message = is_array($flash) ? json_encode($flash) : (string) $flash;
        }

        if(!in_array($this->type, self::VALID_TYPES)){
            throw new InvalidArgumentException(
                sprintf("Invalid flash type '%s'", $this->type)
            );
        }
    }

    public function render(): View|string
    {
        if(!$this->hasFlash){
            return "";
        }

        $name = $this->name;
        $type = $this->type;
        $message = new HtmlString(trans($this->message));
        $dismissible = $this->dismissible;
        $icon = match($this->type){
            self::TYPE_SUCCESS => "circle-check",
            self::TYPE_DANGER => "exclamation-circle",
            self::TYPE_WARNING => "alert-triangle",
            self::TYPE_INFO => "info-circle",
        };

        return view('gui::components.' . ($this->toast ? 'toast' : 'alert'), compact('name', 'icon', 'type', 'message', 'dismissible'));
    }
}