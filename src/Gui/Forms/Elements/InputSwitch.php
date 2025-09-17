<?php
namespace Gui\Forms\Elements;

class InputSwitch extends ChoiceCheckbox
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('label', '');
        $this->addOption('value', 1);
        $this->setOption('choices', []);
        $this->setOption('multiple', false);
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setOption('groupClass', $this->getOption('groupClass') . ' form-switch');
        $this->setOption('choices', [$this->getOption('value') => trans($this->getOption('label'))]);
    }
}