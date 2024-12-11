<?php
namespace Gui\Forms\Validators;

use Illuminate\Support\Collection;

class AndValidator extends AbstractValidator
{
    /**
     * @var Collection<int, AbstractValidator>
     */
    protected Collection $validators;

    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        $this->validators = new Collection();

        parent::initialize();

        $this->addRequiredOption('validators');
    }

    /**
     * @inheritDoc
     */
    protected function beforeValidation() : void
    {
        parent::beforeValidation();

        foreach(collect($this->getOption('validators')) as $validator){
            if(!$validator instanceof AbstractValidator){
                throw new \InvalidArgumentException("[" . get_class($validator) .  "] is not an instance of [" . AbstractValidator::class . "].");
            }

            $this->validators->add($validator);
        }
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v) : mixed
    {
        $this->validators->map(function(AbstractValidator $validator) use (&$v){
            $validator->setFormInstance($this->getFormInstance());
            $validator->setFieldName($this->getFieldName());
            $v = $validator->clean($v);
        });

        return $v;
    }
}