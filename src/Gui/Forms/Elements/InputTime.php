<?php
namespace Gui\Forms\Elements;

class InputTime extends InputDate
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        $this->addOption('icon', 'fa-regular fa-clock');

        parent::initialize();
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setOption('timeOnly', true);
    }
}