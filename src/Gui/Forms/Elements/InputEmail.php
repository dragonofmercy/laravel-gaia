<?php
namespace Gui\Forms\Elements;

class InputEmail extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->setOption('type', 'email');
    }
}