<?php
namespace Gui\Forms\Elements;

use InvalidArgumentException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\ComponentAttributeBag;

use Gui\Forms\Validators\Error;

class EloquentSuggest extends InputAutocomplete
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('model');
        $this->addOption('method', '__toString');
        $this->addOption('externalField');
        $this->addOption('column');

        $this->setAttribute('type', 'hidden');
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-autocomplete-eloquent';
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        $displayAttributes = $this->getAttributes()->except('type', 'id', 'name');
        $displayAttributes['id'] = $this->generateId($name . '_display');
        $displayAttributes['value'] = $this->getValue($value);
        $displayAttributes['type'] = 'text';

        $this->setViewVar('displayAttributes', new ComponentAttributeBag($displayAttributes->toArray()));

        $this->attributes->forget($this->attributes->except('type', 'id')->keys());

        return parent::render($name, $value, $error);
    }

    /**
     * Get value for display field
     *
     * @param mixed $v
     * @return mixed
     */
    protected function getValue(mixed $v): mixed
    {
        if(!empty($v)){
            /** @var Builder $query */
            $query = $this->getOption('model')::query();
            $model = $query->where($this->getColumn(), '=', $v)->first();
            if($model){
                $method = $this->getOption('method');
                if(method_exists($model, $method)){
                    return $model->{$method}();
                } else {
                    throw new InvalidArgumentException("The method [$method] doesn't exists for [" . get_class($model) . "]");
                }
            }
        }

        return $v;
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