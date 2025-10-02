<?php

namespace Gui\Forms\Elements;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

use Gui\Forms\Validators\Error;

class InputFile extends InputGroup
{
    public static string $stringSelectedFiles = 'gui::messages.component.file.selected';
    public static string $stringBrowse = 'gui::messages.component.file.browse';
    public static string $stringEmpty = 'gui::messages.component.file.empty';

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('multiple', false);
        $this->addOption('folder', false);

        $this->setAttribute('type', 'file');
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-file';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        if($this->getOption('folder')){
            $this->setAttribute('webkitdirectory', '');
            $this->setAttribute('mozdirectory', '');
        }

        $this->setViewVar('stringSelectedFiles', trans(self::$stringSelectedFiles));
        $this->setOption('prefix', Blade::render('<a class="link-secondary" data-trigger="browse" title="' . trans(self::$stringBrowse) .'" data-bs-placement="top" data-bs-toggle="tooltip"><x-gui::tabler-icon name="folder-open" /></a>'));
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        if($this->getOption('multiple')){
            $this->setAttribute('multiple', 'multiple');
            if(!Str::endsWith($name, '[]')){
                $name.= '[]';
            }
        }

        $inputAttributes = [
            'type' => 'text',
            'class' => 'form-control',
            'readonly' => 'readonly',
            'placeholder' => $this->getAttribute('placeholder') ?? trans(self::$stringEmpty)
        ];

        $this->setViewVar('inputAttributes', new ComponentAttributeBag($inputAttributes));

        return parent::render($name, null, $error);
    }
}