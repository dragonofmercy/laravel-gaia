<?php

namespace Gui\Forms\Elements;

use Illuminate\Contracts\View\View;
use Gui\Forms\Validators\Error;

class InputToggle extends Input
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('right', 'gui::messages.generic.yes');
        $this->addOption('left', 'gui::messages.generic.no');
        $this->addOption('value', 1);

        $this->setAttribute('type', 'checkbox');
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-toggle';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setAttribute('value', $this->getOption('value'));

        $this->setViewVar('right', trans($this->getOption('right')));
        $this->setViewVar('left', trans($this->getOption('left')));
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        if($value === true){
            $this->setAttribute('checked', 'checked');
        }

        return parent::render($name, $value, $error);
    }
}