<?php

namespace Gui\Datatable\Engines;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;

class EloquentEngine extends AbstractEngine
{
    /**
     * Query builder
     * @var Builder
     */
    protected Builder $query;

    /**
     * Constructor.
     *
     * @param string|Builder $query
     */
    public function __construct(string|Builder $query)
    {
        $this->query = is_string($query) ? call_user_func($query . '::query') : $query;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function initPaginator(): void
    {
        $total = $this->query->count();
        $perPage = $this->getLimit() == 0 ? $total : $this->getLimit();
        $results = $total ? $this->query->forPage($this->currentPage, $perPage)->get(['*']) : new Collection;

        $this->paginator = $this->buildPaginator($results, $total, $perPage, $this->currentPage, []);
    }

    /**
     * @inheritdoc
     */
    protected function sort(): void
    {
        $this->query->orderBy($this->sortBy, $this->sortDirection);
    }

    /**
     * Get query builder
     *
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    protected function getRowColumnValues(string $column): Collection
    {
        $query = clone $this->getQuery();
        return $query->get($column)->transform(function(mixed $value) use ($column){
            $v = $value[$column];
            if($v instanceof \UnitEnum){
                return (string) $v->value;
            }
            return (string) $v;
        })->flip()->flip();
    }
}