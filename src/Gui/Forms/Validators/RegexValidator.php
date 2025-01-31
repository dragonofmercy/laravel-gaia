<?php
namespace Gui\Forms\Validators;

class RegexValidator extends StringValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('pattern');
    }

    /**
     * @inheritDoc
     * @return string
     */
    protected function validate(mixed $v): string
    {
        $clean = parent::validate($v);

        if(!preg_match($this->getOption('pattern'), $clean)){
            throw new Error($this, 'invalid', ['value' => $clean]);
        }

        return $clean;
    }
}