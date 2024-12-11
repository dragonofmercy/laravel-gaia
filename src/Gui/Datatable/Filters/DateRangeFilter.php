<?php
namespace Gui\Datatable\Filters;

use Carbon\Carbon;
use Gui\Datatable\Engines\ArrayEngine;
use Gui\Datatable\Engines\EloquentEngine;
use Throwable;

class DateRangeFilter extends DateFilter
{
    /**
     * Decorator classname
     * @var string
     */
    protected string $decorator = \Gui\Datatable\Decorators\Default\SearchTypes\DateRangeSearchType::class;

    /**
     * @inheritDoc
     */
    public function filter(mixed $value) : void
    {
        $v = collect($value);
        $format = $this->withTime() ? 'Y-m-d H:i:s' : 'Y-m-d';

        try {
            if($v->get('from', false)){
                $v['from'] = Carbon::parse($v->get('from'))->format($format);
            }

            if($v->get('to', false)){
                $v['to'] = Carbon::parse($v->get('to'))->format($format);
            }
        } catch(Throwable){}

        if($this->engine instanceof EloquentEngine){
            if($v->get('from', false)){
                $this->engine->getQuery()->where($this->column, '>=', $v->get('from'));
            }

            if($v->get('to', false)) {
                $this->engine->getQuery()->where($this->column, '<=', $v->get('to'));
            }
        } elseif($this->engine instanceof ArrayEngine) {
            $this->engine->filterCollection(function(array $columns) use ($v){
                $compare = true;
                $current = Carbon::parse($columns[$this->column])->getTimestamp();

                if($v->get('from', false) && $current < Carbon::parse($v->get('from'))->getTimestamp()){
                    $compare = false;
                }

                if($v->get('to', false) && $current > Carbon::parse($v->get('to'))->getTimestamp()) {
                    $compare = false;
                }

                return $compare;
            });
        }
    }
}