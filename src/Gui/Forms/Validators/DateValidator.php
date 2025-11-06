<?php

namespace Gui\Forms\Validators;

use Carbon\Carbon;
use Throwable;

class DateValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('format', 'Y-m-d');
        $this->addOption('errorFormat');
        $this->addOption('min');
        $this->addOption('max');

        $this->setMessage('min', 'gui::validation.date.min');
        $this->setMessage('max', 'gui::validation.date.max');
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v): string
    {
        $outputFormat = $this->getOption('format');
        $errorFormat = $this->hasOption('errorFormat') ? $this->getOption('errorFormat') : $this->getOption('format');

        if($this->getOption('required') && $this->isEmpty($v)){
            throw new Error($this, 'required');
        }

        if(is_array($v)){
            $c = new Carbon();
            foreach($v as $key => $value) {
                $c->set($key, (int) $value);
            }
        } else {
            try {
                $c = Carbon::parse($v);
            } catch(Throwable) {
                throw new Error($this, 'invalid', ['value' => $v]);
            }
        }

        if(!$c->isValid())
        {
            throw new Error($this, 'invalid', ['value' => $v]);
        }

        if($this->hasOption('min') && $c->lessThan($this->getOption('min'))){
            throw new Error($this, 'min', ['value' => $c->format($errorFormat), 'min' => Carbon::parse($this->getOption('min'))->format($errorFormat)]);
        }

        if($this->hasOption('max') && $c->greaterThan($this->getOption('max'))){
            throw new Error($this, 'max', ['value' => $c->format($errorFormat), 'max' => Carbon::parse($this->getOption('max'))->format($errorFormat)]);
        }

        return $c->format($outputFormat);
    }

    /**
     * @inheritDoc
     */
    protected function isEmpty(mixed $value): bool
    {
        if(is_array($value)){
            $value = implode('', $value);
        }

        return parent::isEmpty($value);
    }
}