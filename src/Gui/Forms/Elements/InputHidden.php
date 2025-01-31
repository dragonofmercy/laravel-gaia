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

        $this->setOption('isHidden', true);
        $this->setOption('type', 'hidden');
    }
}