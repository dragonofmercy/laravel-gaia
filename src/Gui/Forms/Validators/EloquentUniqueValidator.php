<?php
namespace Gui\Forms\Validators;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentUniqueValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('model');
        $this->addRequiredOption('column');
        $this->addOption('primaryKey');

        $this->setMessage('invalid', 'gui::validation.choice.unique');
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v) : mixed
    {
        $originalValues = $v;
        $columns = is_array($this->getOption('column')) ? $this->getOption('column') : [$this->getOption('column')];

        if(!$v instanceof Collection){
            $v = collect([$columns[0] => $v]);
        }

        /** @var Builder $query */
        $query = $this->getOption('model')::query();

        foreach($columns as $column){
            if(!$v->has($column)){
                return $originalValues;
            }

            $query->where($column, "=", $v[$column]);
        }

        $model = $query->first();

        if(!$model || $this->isUpdate($model, $v)){
            return $originalValues;
        }

        throw new Error($this, 'invalid');
    }

    /**
     * Get if current record is an update
     *
     * @param Model $model
     * @param Collection $v
     * @return bool
     */
    protected function isUpdate(Model $model, Collection $v) : bool
    {
        $primaryKey = $this->getPrimaryKey();
        return isset($v[$primaryKey]) && $model->$primaryKey == $v[$primaryKey];
    }

    /**
     * Get primary key
     *
     * @return string
     */
    protected function getPrimaryKey() : string
    {
        if($this->hasOption('primaryKey')){
            return $this->getOption('primaryKey');
        } else {
            $modelClass = $this->getOption('model');
            return (new $modelClass)->getKeyName();
        }
    }
}