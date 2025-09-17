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

        $this->addOption('min', 0);
        $this->addOption('max', 100);
        $this->addOption('step', 1);
        $this->addOption('unlimitedValue');
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

        $this->appendAttribute('class', 'text-center');
        $this->setAttribute('autocomplete', 'off');
        $this->setAttribute('inputmode', 'numeric');

        $componentConfig = [
            'min' => $this->getOption('min'),
            'max' => $this->getOption('max'),
            'step' => $this->getOption('step'),
            'unlimitedValue' => $this->getOption('unlimitedValue')
        ];

        $this->setViewVar('componentConfig', json_encode($componentConfig));
    }
}