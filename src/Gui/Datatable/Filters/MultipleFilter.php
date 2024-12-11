<?php
namespace Gui\Datatable\Filters;

use Demeter\Support\Str;
use Gui\Datatable\Engines\ArrayEngine;
use Gui\Datatable\Engines\EloquentEngine;
use Illuminate\Database\Eloquent\Builder;

class MultipleFilter extends TextFilter
{
    /**
     * Set or get fields
     *
     * @param array|null $columns
     * @return array|MultipleFilter
     */
    public function columns(array|null $columns = null) : self|array
    {
        if(null === $columns){
            return $this->options->get('columns', []);
        } else {
            $this->options['columns'] = $columns;
            return $this;
        }
    }

    /**
     * @inheritDoc
     */
    public function filter(mixed $value) : void
    {
        $searchItems = explode(' ', (string) $value);

        if($this->engine instanceof EloquentEngine){
            $searchQuery = [];

            foreach($this->columns() as $column){
                foreach($searchItems as $item){
                    if(in_array($this->operator(), ['LIKE', 'ILIKE'])){
                        $item = "%$item%";
                    }
                    $searchQuery[] = [$column, $this->operator(), $item];
                }
            }

            if(count($searchQuery)){
                $this->engine->getQuery()->where(function(Builder $query) use ($searchQuery){
                    foreach($searchQuery as $search){
                        $query->orWhere($search[0], $search[1], $search[2]);
                    }
                });
            }
        } elseif($this->engine instanceof ArrayEngine) {
            $this->engine->filterCollection(function(array $v) use ($searchItems){
                $return = false;
                foreach($this->columns() as $column){
                    foreach($searchItems as $item){
                        if(in_array($this->operator(), ['LIKE', 'ILIKE'])){
                            if(Str::contains($v[$column], $item, true)){
                                $return = true;
                            }
                        } elseif($this->operator() === '=') {
                            if(strcasecmp($v[$column], $item) === 0){
                                $return = true;
                            }
                        } else {
                            if(eval('return "' . addcslashes($v[$column], '"') . '"' . $this->operator() . '"' . addcslashes($item, '"') . '";')){
                                $return = true;
                            }
                        }
                    }
                }
                return $return;
            });
        }
    }
}