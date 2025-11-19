<?php

namespace Gui\Forms\Elements;

class InputNumber extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('min', null);
        $this->addOption('max', null);
        $this->addOption('step', 1);
        $this->addOption('unlimitedValue');
        $this->addOption('displayButtons', true);
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-number';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setAttribute('autocomplete', 'off');
        $this->setAttribute('inputmode', 'numeric');

        $componentConfig = [
            'min' => $this->getOption('min'),
            'max' => $this->getOption('max'),
            'step' => $this->getOption('step'),
            'unlimitedValue' => $this->getOption('unlimitedValue'),
        ];

        $this->setViewVar('displayButtons', $this->getOption('displayButtons'));
        $this->setViewVar('componentConfig', json_encode($componentConfig));
    }
}