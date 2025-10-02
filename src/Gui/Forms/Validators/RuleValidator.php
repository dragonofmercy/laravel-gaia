<?php

namespace Gui\Forms\Validators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class RuleValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('rule');
        $this->addOption('errorMessagesSeparator', '<br />');
        $this->addOption('errorMessages');
    }

    /**
     * @inheritDoc
     */
    protected function beforeValidation(): void
    {
        parent::beforeValidation();

        if(!is_string($this->getOption('rule'))){
            throw new InvalidArgumentException('The rule must be a string, Fluent validation is not supported');
        }

        $this->setOption('required', Str::contains($this->getOption('rule'), 'required'));

        if($this->hasOption('errorMessages')){
            $messages = collect($this->getOption('errorMessages'));

            if($messages->has('required')){
                $this->setMessage('required', $messages->get('required'));
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v): mixed
    {
        $fieldName = '';
        $validator = Validator::make(
            [$fieldName => $v],
            [$fieldName => $this->getOption('rule')]
        );

        if($this->hasOption('errorMessages')){
            $validator->setCustomMessages([$fieldName => $this->getOption('errorMessages')]);
        }

        if($validator->fails()){
            $this->setMessage('rule', implode($this->getOption('errorMessagesSeparator'), $validator->errors()->all()));
            throw new Error($this, 'rule');
        }

        return $v;
    }
}