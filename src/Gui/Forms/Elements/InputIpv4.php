<?php

namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;
use Illuminate\Contracts\View\View;

class InputIpv4 extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->appendAttribute('class', 'input-group input-group-flat gui-control-ipv4');
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-ipv4';
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        $matrix = [null, null, null, null];
        $this->setAttribute('name', $name . '[]');

        if(is_array($value)){
            $matrix = array_replace($matrix, $value);
        } elseif(!empty((string) $value)) {
            $matrix = explode('.', (string) $value);
        }

        $this->setViewVar('valueMatrix', $matrix);

        return parent::render($name, $value, $error);
    }
}