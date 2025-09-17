<?php
namespace Gui\Forms\Elements;

class InputRange extends Input
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
        $this->addOption('progress', true);

        $this->setAttribute('type', 'range');
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->appendAttribute('class', 'form-range');

        if($this->getOption('progress')){
            $this->appendAttribute('class', 'with-progress');
        }

        $this->attributes['min'] = $this->getOption('min');
        $this->attributes['max'] = $this->getOption('max');
        $this->attributes['step'] = $this->getOption('step');
    }
}