<?php

namespace Gui\Forms\Elements;

class InputAutocomplete extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('url');
        $this->addOption('minLength', 1);
        $this->addOption('valueField', 'value');
        $this->addOption('textField', 'text');
        $this->addOption('limit', 10);
        $this->addOption('reference', 'self');
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-autocomplete';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $componentConfig = [
            'provider' => url($this->getOption('url')),
            'minLength' => $this->getOption('minLength'),
            'valueField' => $this->getOption('valueField'),
            'textField' => $this->getOption('textField'),
            'limit' => $this->getOption('limit'),
            'reference' => $this->getOption('reference')
        ];

        $this->setViewVar('componentConfig', json_encode($componentConfig));
        $this->setAttribute('autocomplete', 'off');
    }
}