<?php
namespace Gui\Forms\Validators;

use Gui\Forms\FormEloquent;

class NoChangeValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->setOption('required', false);
    }

    /**
     * @inheritDoc
     */
    public function clean(mixed $v): mixed
    {
        return $this->validate($v);
    }

    /**
     * @inheritDoc
     * @return null
     */
    protected function validate(mixed $v): mixed
    {
        if($this->getFormInstance() instanceof FormEloquent){
            $value = $this->getFormInstance()->getModel()->{$this->getFieldName()};
            return null === $value ? $this->getFormInstance()->getDefault($this->getFieldName()) : $value;
        } else {
            return $this->getFormInstance()->getDefault($this->getFieldName());
        }
    }
}