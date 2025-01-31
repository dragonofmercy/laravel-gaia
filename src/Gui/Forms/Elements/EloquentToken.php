<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentToken extends ChoiceToken
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('model');
        $this->addOption('column');
        $this->addOption('method', '__toString');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        if(is_array($value)){
            $value = collect($value)->map(function(mixed $v){
                return is_array($v) ? $v[$this->getColumn()] : $v;
            })->toArray();
        }

        return parent::render($name, $value, $error);
    }

    /**
     * @inheritDoc
     */
    protected function getChoices(): Collection
    {
        $choices = new Collection();
        $values = collect($this->getFormInstance()->getValue($this->getFieldName()));

        if(empty($values)){
            return $choices;
        }

        $values = $values->map(function(mixed $v){
            return is_array($v) ? $v[$this->getColumn()] : $v;
        });

        $method = $this->getOption('method');

        /** @var Builder $query */
        $query = $this->getOption('model')::query();
        $query->whereIn($this->getColumn(), $values)->get()->map(function(mixed $model) use (&$choices, $method){
            if(method_exists($model, $method)){
                $choices[$model->{$this->getColumn()}] = $model->{$this->getOption('method')}();
            } else {
                throw new \InvalidArgumentException("The method [$method] doesn't exists for [" . get_class($model) . "]");
            }
        });

        return $choices;
    }

    /**
     * Get column name
     *
     * @return string
     */
    protected function getColumn(): string
    {
        if($this->hasOption('column')){
            return $this->getOption('column');
        } else {
            $modelClass = $this->getOption('model');
            return (new $modelClass)->getKeyName();
        }
    }
}