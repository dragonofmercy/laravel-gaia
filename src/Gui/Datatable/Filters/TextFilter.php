<?php

namespace Gui\Datatable\Filters;

use Demeter\Support\Str;
use Gui\Datatable\Engines\ArrayEngine;
use Gui\Datatable\Engines\EloquentEngine;

class TextFilter extends AbstractFilter
{
    /**
     * @inheritDoc
     */
    protected string $decorator = \Gui\Datatable\Filters\Decorators\TextDecorator::class;

    /**
     * @inheritDoc
     */
    public function filter(mixed $value): void
    {
        if($this->engine instanceof EloquentEngine){
            if(in_array($this->operator(), ['LIKE', 'ILIKE'])){
                $value = "%$value%";
            }
            $this->engine->getQuery()->where($this->column, $this->operator(), $value);
        } elseif($this->engine instanceof ArrayEngine) {
            $this->engine->filterCollection(function(array $v) use ($value){
                if(array_key_exists($this->column, $v)){
                    if(in_array($this->operator(), ['LIKE', 'ILIKE'])){
                        return Str::contains($v[$this->column], $value, true);
                    } elseif($this->operator() === '=') {
                        return strcasecmp($v[$this->column], $value) === 0;
                    } else {
                        return eval('return "' . addcslashes($v[$this->column], '"') . '"' . $this->operator() . '"' . addcslashes($value, '"') . '";');
                    }
                }
                return false;
            });
        }
    }
}