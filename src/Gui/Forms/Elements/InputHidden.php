<?php
namespace Gui\Forms\Elements;

class InputHidden extends Input
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->setAttribute('type', 'hidden');
        $this->setOption('isHidden', true);
    }
}