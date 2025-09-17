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
    public function validateOptions(array|Collection $options = []): void
    {
        $this->setOption('choices', []);
        parent::validateOptions($options);
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
    protected function beforeRender(): void
    {
        parent::beforeRender();
        $this->loadChoices();
    }

    /**
     * Load and prepare choices based on the defined method and model.
     *
     * @return void
     */
    protected function loadChoices()
    {
        $method = $this->getOption('method');

        /** @var Builder $query */
        $query = $this->getOption('model')::query();
        $column = $this->getColumn();
        $choices = new Collection();

        $query->get()->map(function(mixed $model) use ($method, $column, &$choices){
            if(method_exists($model, $method)){
                $choices->put($model->$column, $model->{$this->getOption('method')}());
            } else {
                throw new \InvalidArgumentException("The method [$method] doesn't exists for [" . get_class($model) . "]");
            }
        });

        $this->setOption('choices', $choices);
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