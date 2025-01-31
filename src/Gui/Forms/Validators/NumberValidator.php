<?php
namespace Gui\Forms\Validators;

class NumberValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('min');
        $this->addOption('max');
        $this->addOption('round');
        $this->addOption('mode', PHP_ROUND_HALF_UP);

        $this->setMessage('min', 'gui::validation.number.min');
        $this->setMessage('max', 'gui::validation.number.max');
        $this->setMessage('invalid', 'gui::validation.number.invalid');
    }

    /**
     * @inheritDoc
     * @return float
     */
    protected function validate(mixed $v): float
    {
        $clean = floatval($v);

        if(strval($v) != $clean){
            throw new Error($this, 'invalid', ['value' => $v]);
        }

        if($this->hasOption('round')){
            $clean = round($clean, $this->getOption('round'), $this->getOption('mode'));
        }

        if($this->hasOption('min') && $clean < $this->getOption('min')){
            throw new Error($this, 'min', ['value' => $clean, 'min' => $this->getOption('min')]);
        }

        if($this->hasOption('max') && $clean > $this->getOption('max')){
            throw new Error($this, 'max', ['value' => $clean, 'max' => $this->getOption('max')]);
        }

        return $clean;
    }
}