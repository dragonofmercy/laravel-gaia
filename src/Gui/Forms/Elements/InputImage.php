<?php

namespace Gui\Forms\Elements;

class InputImage extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('size', [128, 128]);
        $this->addOption('viewMode', 1);
        $this->addOption('fileTypes', ['image/jpeg', 'image/png']);
        $this->addOption('exportType', 'image/jpeg');
        $this->addOption('exportQuality', 1.0);
        $this->addOption('accept', '.png,.jpg,.jpeg');
        $this->addOption('guides', true);

        $this->setAttribute('type', 'hidden');
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-image';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setViewVar('sizeDisplay', implode('x', $this->getOption('size')));
        $this->setViewVar('sizeStyle', 'width:' . $this->getOption('size')[0] . 'px;height:' . $this->getOption('size')[1] . 'px;');

        $componentConfig = [
            'viewMode' => $this->getOption('viewMode'),
            'guides' => $this->getOption('guides') ? "true" : "false",
            'width' => $this->getOption('size')[0],
            'height' => $this->getOption('size')[1],
            'accept' => $this->getOption('accept'),
            'fileTypes' => implode(',', $this->getOption('fileTypes')),
            'exportType' => $this->getOption('exportType'),
            'exportQuality' => $this->getOption('exportQuality')
        ];

        $this->setViewVar('componentConfig', json_encode($componentConfig));
    }
}