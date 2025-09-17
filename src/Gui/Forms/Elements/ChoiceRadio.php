<?php
namespace Gui\Forms\Elements;

class ChoiceRadio extends ChoiceCheckbox
{
    /**
     * @inheritDoc
     */
    public function generateId(string $name, mixed $value = null): string
    {
        return parent::generateId($name . '[]', $value);
    }

    /**
     * @inheritDoc
     */
    protected function getInputType(): string
    {
        return 'radio';
    }
}