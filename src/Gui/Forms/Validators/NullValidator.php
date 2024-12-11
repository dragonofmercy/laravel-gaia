<?php
namespace Gui\Forms\Validators;

class NullValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->setOption('required', false);
    }

    /**
     * @inheritDoc
     * @return null
     */
    protected function validate(mixed $v) : null
    {
        return null;
    }
}