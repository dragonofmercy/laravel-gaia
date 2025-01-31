<?php
namespace Gui\Forms\Validators;

class BooleanValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('trueValues', ['true', 't', 'yes', 'y', 'on', '1']);
        $this->addOption('falseValues', ['false', 'f', 'no', 'n', 'off', '0']);

        $this->setOption('required', false);
        $this->setOption('emptyValue', false);
    }

    /**
     * @inheritDoc
     * @return boolean
     */
    protected function validate(mixed $v): bool
    {
        if(in_array($v, $this->getOption('trueValues'))){
            return true;
        }

        if(in_array($v, $this->getOption('falseValues'))){
            return false;
        }

        throw new Error($this, 'invalid', ['value' => $v]);
    }
}