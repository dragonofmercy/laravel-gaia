<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;
use Illuminate\View\View;

class TextareaLines extends Textarea
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('max', 0);
        $this->addOption('autosize', false);
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.textarea-lines';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setViewVar('autosize', $this->getOption('autosize'));
        $this->setViewVar('componentConfig', json_encode([
            'max' => $this->getOption('max')
        ]));
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        if(is_iterable($value)){
            $nbLines = count($value);
            $value = implode("\n", $value);
        } else {
            $normalizedValue = str_replace(["\r\n", "\r"], "\n", (string) $value);
            $nbLines = empty($normalizedValue) ? 1 : substr_count($normalizedValue, "\n") + 1;
        }

        $this->setViewVar('nbLines', $nbLines);
        return parent::render($name, $value, $error);
    }
}