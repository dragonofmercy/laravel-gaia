<?php
namespace Gui\Forms\Elements;

class InputText extends Input
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->appendAttribute('class', 'form-control');
        $this->setAttribute('type', 'text');
    }
}