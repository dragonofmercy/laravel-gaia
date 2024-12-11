<?php
namespace Gui\Forms\Validators;

use Illuminate\Support\Collection;

class CompareValidator extends AbstractValidator
{
    const EQUAL = '==';
    const NOT_EQUAL = '!=';
    const IDENTICAL = '===';
    const NOT_IDENTICAL = '!==';
    const LESS_THAN = '<';
    const LESS_THAN_EQUAL = '<=';
    const GREATER_THAN = '>';
    const GREATER_THAN_EQUAL = '>=';

    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('left');
        $this->addRequiredOption('right');
        $this->addOption('operator', self::EQUAL);

        $this->setOption('emptyValue', new Collection());
    }

    /**
     * @inheritDoc
     */
    protected function beforeValidation() : void
    {
        parent::beforeValidation();
        
        if(!$this->hasFlag(self::FLAG_FIELD)){
            $this->setFlag(self::FLAG_FIELD, $this->getOption('right'));
        }
    }

    /**
     * @inheritDoc
     * @return array
     */
    protected function validate(mixed $v) : Collection
    {
        if(!$v instanceof Collection){
            throw new \RuntimeException("You must pass a [" . Collection::class . "] to the [validate] method in [" . get_class($this) . "]");
        }

        $leftValue = $v->get($this->getOption('left'));
        $rightValue = $v->get($this->getOption('right'));

        $valid = match ($this->getOption('operator')) {
            self::GREATER_THAN => $leftValue > $rightValue,
            self::GREATER_THAN_EQUAL => $leftValue >= $rightValue,
            self::LESS_THAN => $leftValue < $rightValue,
            self::LESS_THAN_EQUAL => $leftValue <= $rightValue,
            self::NOT_EQUAL => $leftValue != $rightValue,
            self::EQUAL => $leftValue == $rightValue,
            self::NOT_IDENTICAL => $leftValue !== $rightValue,
            self::IDENTICAL => $leftValue === $rightValue,
            default => throw new \InvalidArgumentException("The operator [" . $this->getOption('operator') . "] does not exist."),
        };

        if(!$valid){
            throw new Error($this, 'invalid');
        }

        return $v;
    }
}