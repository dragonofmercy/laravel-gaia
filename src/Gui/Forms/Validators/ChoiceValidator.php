<?php

namespace Gui\Forms\Validators;

use Illuminate\Support\Collection;

class ChoiceValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('choices', []);
        $this->addOption('multiple', false);
        $this->addOption('min');
        $this->addOption('max');

        $this->setMessage('required', 'gui::validation.choice.required');
        $this->setMessage('min', 'gui::validation.choice.min');
        $this->setMessage('max', 'gui::validation.choice.max');
    }

    /**
     * @inheritDoc
     * @return mixed
     */
    protected function validate(mixed $v): mixed
    {
        if($this->getOption('multiple') === true){
            return $this->validateMultiple($v, collect($this->getOption('choices')));
        } else {
            if(!$this->inChoices($v, collect($this->getOption('choices')))){
                throw new Error($this, 'invalid', ['value' => $v]);
            }
        }

        return $v;
    }

    /**
     * Validate multiple values
     *
     * @param mixed $values
     * @param Collection $choices
     * @return array
     */
    protected function validateMultiple(mixed $values, Collection $choices): array
    {
        $values = collect($values);

        if(!$choices->isEmpty()){
            $values->map(function(mixed $v) use ($choices){
                if(!$this->inChoices($v, $choices)){
                    throw new Error($this, 'invalid', ['value' => $v]);
                }
                return $v;
            });
        }

        if($this->hasOption('min') && $values->count() < $this->getOption('min')){
            throw new Error($this, 'min', ['count' => $values->count(), 'min' => $this->getOption('min')]);
        }

        if($this->hasOption('max') && $values->count() > $this->getOption('max')){
            throw new Error($this, 'max', ['count' => $values->count(), 'max' => $this->getOption('max')]);
        }

        return $values->toArray();
    }

    /**
     * Check if value is part of given choices
     *
     * @param mixed $value
     * @param Collection $choices
     * @return bool
     */
    protected function inChoices(mixed $value, Collection $choices): bool
    {
        foreach($choices as $index => $choice){
            if(is_iterable($choice)){
                if($this->inChoices($value, collect($choice))){
                    return true;
                }
            }

            if((string) $index == (string) $value){
                return true;
            }
        }

        return false;
    }
}