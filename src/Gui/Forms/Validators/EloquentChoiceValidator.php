<?php
namespace Gui\Forms\Validators;

use Illuminate\Database\Eloquent\Builder;

class EloquentChoiceValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('model');
        $this->addOption('column');
        $this->addOption('query');
        $this->addOption('multiple', false);
        $this->addOption('min');
        $this->addOption('max');

        $this->setMessage('required', 'gui::validation.choice.required');
        $this->setMessage('min', 'gui::validation.choice.min');
        $this->setMessage('max', 'gui::validation.choice.max');
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v) : mixed
    {
        if($this->hasOption('query') && $this->getOption('query') instanceof Builder){
            /** @var Builder $query */
            $query = clone $this->getOption('query');
        } else {
            /** @var Builder $query */
            $query = $this->getOption('model')::query();
        }

        if($this->getOption('multiple')){
            if(!is_array($v)){
                $v = [$v];
            }

            $count = count($v);

            if($this->hasOption('min') && $count < $this->getOption('min')){
                throw new Error($this, 'min', ['count' => $count, 'min' => $this->getOption('min')]);
            }

            if($this->hasOption('max') && $count > $this->getOption('max')){
                throw new Error($this, 'max', ['count' => $count, 'max' => $this->getOption('max')]);
            }

            $query->whereIn($this->getColumn(), $v);

            if($query->count() != $count){
                throw new Error($this, 'invalid', ['value' => implode(', ', $v)]);
            }
        } else {
            $query->where($this->getColumn(), '=', $v);

            if(!$query->count()){
                throw new Error($this, 'invalid', ['value' => $v]);
            }
        }

        return $v;
    }

    /**
     * Get column name
     *
     * @return string
     */
    protected function getColumn() : string
    {
        if($this->hasOption('column')){
            return $this->getOption('column');
        } else {
            $modelClass = $this->getOption('model');
            return (new $modelClass)->getKeyName();
        }
    }
}