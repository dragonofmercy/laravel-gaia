<?php
namespace Gui\Forms\Validators;

class LineValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('validator', StringValidator::class);
        $this->addOption('removeDuplicates', false);
        $this->addOption('min');
        $this->addOption('max');

        $this->setMessage('min', 'gui::validation.line.min');
        $this->setMessage('max', 'gui::validation.line.max');
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v) : array
    {
        $lines = collect(preg_split('/\r?\n/', (string) $v));
        $validator = $this->getOption('validator');

        if(!$validator instanceof AbstractValidator){
            $validator = new $validator();
        }

        $lines->transform(function(mixed $v) use ($validator){
            return $validator->clean($v);
        });

        if($this->getOption('removeDuplicates')){
            $lines = $lines->unique();
        }

        if($this->hasOption('min') && $lines->count() < $this->getOption('min')){
            throw new Error($this, 'min', ['count' => $lines->count(), 'min' => $this->getOption('min')]);
        }

        if($this->hasOption('max') && $lines->count() > $this->getOption('max')){
            throw new Error($this, 'max', ['count' => $lines->count(), 'max' => $this->getOption('max')]);
        }

        return $lines->toArray();
    }
}