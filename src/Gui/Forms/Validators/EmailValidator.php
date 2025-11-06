<?php

namespace Gui\Forms\Validators;

use Egulias\EmailValidator\EmailValidator as BaseEmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\Extra\SpoofCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use Gui\Forms\Validators\Formatters\LowerCase;
use Illuminate\Validation\Concerns\FilterEmailValidation;

class EmailValidator extends StringValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('checkMx', true);
        $this->addOption('checkSpoof', true);
        $this->addOption('formatter', LowerCase::class);

        $this->setMessage('invalid', 'gui::validation.email.invalid');
    }

    /**
     * @inheritDoc
     * @return string
     */
    protected function validate(mixed $v): string
    {
        $v = parent::validate($v);

        $validators = [new RFCValidation(), new FilterEmailValidation()];

        if($this->getOption('checkSpoof')){
            $validators[] = new SpoofCheckValidation();
        }

        if($this->getOption('checkMx')){
            $validators[] = new DNSCheckValidation();
        }

        $emailValidator = new BaseEmailValidator();
        $multipleValidations = new MultipleValidationWithAnd($validators);
        if(!$emailValidator->isValid($v, $multipleValidations)){
            throw new Error($this, 'invalid', ['value' => $v]);
        }

        return $v;
    }
}