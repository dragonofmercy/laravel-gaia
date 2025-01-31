<?php
namespace Gui\Datatable\Filters;

use Gui\Datatable\Engines\ArrayEngine;
use Gui\Datatable\Engines\EloquentEngine;

class BooleanFilter extends ChoicesFilter
{
    /**
     * Decorator classname
     * @var string
     */
    protected string $decorator = \Gui\Datatable\Decorators\Default\SearchTypes\ChoicesSearchType::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
        $this->options['choices'] = [
            1 => trans('gui::messages.generic.yes'),
            0 => trans('gui::messages.generic.no')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filter(mixed $value): void
    {
        if($this->engine instanceof EloquentEngine){
            $this->engine->getQuery()->where($this->column, "=", $value);
        } elseif($this->engine instanceof ArrayEngine) {
            $this->engine->filterCollection(function(array $v) use ($value){
                if(array_key_exists($this->column, $v)){
                    return $v[$this->column] === $value;
                }
                return false;
            });
        }
    }
}