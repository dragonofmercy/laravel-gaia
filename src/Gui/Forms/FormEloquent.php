<?php

namespace Gui\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB as DBFacade;

abstract class FormEloquent extends Form
{
    /**
     * Model object
     * @var Model|mixed|null
     */
    protected Model|null $model = null;

    /**
     * Current model status
     * @var bool
     */
    protected bool $isNew = true;

    /**
     * Constructor.
     *
     * @param Model|null $model
     * @param array $defaults
     */
    public function __construct(Model|null $model = null, array $defaults = [])
    {
        $class = $this->getModelClass();

        if(null === $model){
            $this->model = new $class();
        } else {
            if(!$model instanceof $class){
                throw new \InvalidArgumentException("The form [" . get_class($this) . "] only accepts a [" . $class . "] object.");
            }

            $this->model = $model;
            $this->isNew = !$this->getModel()->exists;
        }

        parent::__construct($defaults);
        $this->updateDefaultsFromModel();
    }

    /**
     * Save the current object to the database
     *
     * @return Model
     */
    public function save(): Model
    {
        if(!$this->isValid()){
            throw new \RuntimeException('The form is not valid, cannot do save');
        }

        try {
            DBFacade::beginTransaction();
            $this->_save();
            DBFacade::commit();
        } catch(\Throwable $e) {
            DBFacade::rollBack();
            throw $e;
        }

        return $this->model;
    }

    /**
     * Get current model
     *
     * @return Model|null
     */
    public function getModel(): Model|null
    {
        return $this->model;
    }

    /**
     * Return if the form is new
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * Update the default values of the form with the current values
     * of the current model.
     *
     * @return void
     */
    protected function updateDefaultsFromModel(): void
    {
        $defaults = $this->getDefaults()->toArray();

        if($this->isNew()){
            $defaults = $defaults + $this->getModel()->toArray();
        } else {
            $defaults = $this->getModel()->toArray() + $defaults;
        }

        $this->setDefaults($defaults);
    }

    /**
     * Update the values of the model with the cleaned up values.
     *
     * @param Collection|array|null $values
     * @return Model|null
     */
    protected function prepareModel(Collection|array|null $values = null): Model|null
    {
        if(null === $values){
            $values = $this->values;
        }

        $this->updateModel($values);

        return $this->getModel();
    }

    /**
     * Update the values of the model with the cleaned up values.
     *
     * If you want to add some logic before updating or update other associated
     * models, this is the method to override.
     *
     * @param Collection|array $values
     * @return void
     */
    protected function updateModel(Collection|array $values): void
    {
        if($values instanceof Collection){
            $values = $values->toArray();
        }

        $fillable = $this->getModel()->getFillable();

        foreach(array_keys($values) as $column){
            if(!in_array($column, $fillable)){
                unset($values[$column]);
            }
        }

        $this->getModel()->fill($values);
    }

    /**
     * Update and saves the current model.
     *
     * @return Model
     */
    protected function _save(): Model
    {
        $this->prepareModel();
        $this->getModel()->save();

        return $this->getModel();
    }

    /**
     * Get model classname
     *
     * @return string
     */
    abstract public function getModelClass(): string;
}