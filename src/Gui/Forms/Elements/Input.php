<?php

namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;
use Illuminate\Contracts\View\View;

abstract class Input extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        if(!$this->hasAttribute('autocomplete') && in_array($this->getAttribute('type'), ['text', 'email', 'password'])){
            $this->setAttribute('autocomplete', 'off');
        }
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        $this->setAttribute('name', $name);
        $this->setAttribute('value', $value);

        return parent::render($name, $value, $error);
    }
}