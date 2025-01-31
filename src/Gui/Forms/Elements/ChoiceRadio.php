<?php
namespace Gui\Forms\Elements;

class ChoiceRadio extends ChoiceCheckbox
{
    /**
     * Input type
     * @var string
     */
    protected string $inputType = 'radio';

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        $this->addOption('class', 'gui-form-radio');

        parent::initialize();
    }

    /**
     * @inheritDoc
     */
    protected function getInputName(string $name, int $count): string
    {
        return $name;
    }

    /**
     * @inheritDoc
     */
    public function generateId(string $name, mixed $value = null): string
    {
        return parent::generateId($name . '[]', $value);
    }
}