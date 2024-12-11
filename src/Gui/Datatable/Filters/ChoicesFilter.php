<?php
namespace Gui\Datatable\Filters;

use Gui\Datatable\Engines\ArrayEngine;
use Gui\Datatable\Engines\EloquentEngine;
use Illuminate\Support\Collection;

class ChoicesFilter extends AbstractFilter
{
    /**
     * Decorator classname
     * @var string
     */
    protected string $decorator = \Gui\Datatable\Decorators\Default\SearchTypes\ChoicesSearchType::class;

    /**
     * Set or get choices
     *
     * @param array|Collection|null $choices
     * @return ChoicesFilter|array
     */
    public function choices(array|Collection|null $choices = null) : self|array
    {
        if(null === $choices){
            return $this->options->get('choices', []);
        } else {
            $this->options['choices'] = $choices instanceof Collection ? $choices->toArray() : $choices;
            return $this;
        }
    }

    /**
     * Set or get add_empty
     *
     * @param string|null $v
     * @return string|$this|self
     */
    public function addEmpty(string|null $v = null) : self|string
    {
        if(null === $v){
            return $this->options->get('add_empty', "");
        } else {
            $this->options['add_empty'] = $v;
            return $this;
        }
    }

    /**
     * Set or get multiple
     *
     * @param bool|null $v
     * @return bool|$this|self
     */
    public function multiple(bool|null $v = null) : self|bool
    {
        if(null === $v){
            return $this->options->get('multiple', false);
        } else {
            $this->options['multiple'] = $v;
            return $this;
        }
    }

    /**
     * @inheritDoc
     */
    public function filter(mixed $value) : void
    {
        if($this->engine instanceof EloquentEngine){
            if($this->multiple()){
                $this->engine->getQuery()->whereIn($this->column, $value);
            } else {
                $this->engine->getQuery()->where($this->column, "=", $value);
            }
        } elseif($this->engine instanceof ArrayEngine) {
            $this->engine->filterCollection(function(array $v) use ($value){
                if(array_key_exists($this->column, $v)){
                    if($this->multiple()){
                        foreach($value as $multipleValue){
                            if(strcmp($v[$this->column], $multipleValue) === 0){
                                return true;
                            }
                        }
                    } else {
                        return strcmp($v[$this->column], $value) === 0;
                    }
                }
                return false;
            });
        }
    }
}