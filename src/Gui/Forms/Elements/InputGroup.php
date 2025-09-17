<?php
namespace Gui\Forms\Elements;

use Illuminate\Contracts\View\View;
use Gui\Forms\Validators\Error;
use Illuminate\Support\HtmlString;

class InputGroup extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('prefix');
        $this->addOption('suffix');
        $this->addOption('stripValue', true);
        $this->addOption('flat', true);
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-group';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setViewVar('flat', $this->getOption('flat'));
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        return parent::render($name, $this->prepareValue($value), $error);
    }

    /**
     * Prepares the given value based on the configured options.
     *
     * @param mixed $value The value to be prepared.
     * @return mixed The processed value after applying the prefix, suffix, and optional stripping logic.
     */
    protected function prepareValue(mixed $value): mixed
    {
        $value = $value ?? '';

        $this->setViewVar('prefix', null !== $this->getOption('prefix') ? new HtmlString($this->getOption('prefix')) : null);
        $this->setViewVar('suffix', null !== $this->getOption('suffix') ? new HtmlString($this->getOption('suffix')) : null);

        if(!$this->getOption('stripValue')){
            return $value;
        }

        if($this->getOption('prefix')){
            $value = $this->cleanValueBasedOnPosition($value, $this->getOption('prefix'), 'prefix');
        }

        if($this->getOption('suffix')){
            $value = $this->cleanValueBasedOnPosition($value, $this->getOption('suffix'), 'suffix');
        }

        return $value;
    }

    /**
     * Cleans a given value by removing the specified addon based on its position (prefix or suffix).
     *
     * @param string $value The original value to be cleaned.
     * @param string $addon The string that should be removed from the value.
     * @param string $position The position of the addon in the value. Can be 'prefix' or 'suffix'. Defaults to 'prefix'.
     * @return string The cleaned value with the addon removed based on the specified position.
     */
    protected function cleanValueBasedOnPosition(string $value, string $addon, string $position = 'prefix'): string
    {
        return match ($position){
            'prefix' => preg_replace('/^' . preg_quote($addon, '/') . '/', '', $value),
            'suffix' => preg_replace('/' . preg_quote($addon, '/') . '$/', '', $value),
        };
    }
}