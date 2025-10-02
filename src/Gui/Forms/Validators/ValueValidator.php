<?php

namespace Gui\Forms\Validators;

class ValueValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('value');
        $this->addOption('displayError', false);
    }

    /**
     * @inheritDoc
     * @return mixed
     */
    protected function validate(mixed $v): mixed
    {
        if($this->getOption('displayError')){
            if((string) $v != (string) $this->getOption('value')){
                throw new Error($this, 'invalid');
            }
        }

        return $this->getOption('value');
    }
}