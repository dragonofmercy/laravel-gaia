<?php

namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;
use Illuminate\View\View;

class Textarea extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->appendAttribute('class', 'form-control');
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.textarea';
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        $this->setAttribute('name', $name);
        $this->setViewVar('value', $value);

        return parent::render($name, $value, $error);
    }
}