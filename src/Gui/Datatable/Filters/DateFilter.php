<?php
namespace Gui\Datatable\Filters;

use Carbon\Carbon;
use Gui\Datatable\Engines\ArrayEngine;
use Gui\Datatable\Engines\EloquentEngine;
use Throwable;

class DateFilter extends AbstractFilter
{
    /**
     * Decorator classname
     * @var string
     */
    protected string $decorator = \Gui\Datatable\Decorators\Default\SearchTypes\DateSearchType::class;

    /**
     * Set or get with time
     *
     * @param bool|null $v
     * @return bool|DateFilter
     */
    public function withTime(bool|null $v = null) : self|bool
    {
        if(null === $v){
            return $this->options->get('with_time', false);
        } else {
            $this->options['with_time'] = $v;
            return $this;
        }
    }

    /**
     * @inheritDoc
     */
    public function filter(mixed $value) : void
    {
        try {
            $format = $this->withTime() ? 'Y-m-d H:i:s' : 'Y-m-d';
            $value = Carbon::parse($value)->format($format);
        } catch(Throwable){}

        if($this->engine instanceof EloquentEngine){
            $this->engine->getQuery()->where($this->column, "=", $value);
        } elseif($this->engine instanceof ArrayEngine) {
            $this->engine->filterCollection(function(array $v) use ($value){
                if(array_key_exists($this->column, $v)){
                    return strcmp($v[$this->column], $value) === 0;
                }
                return false;
            });
        }
    }
}