<?php

namespace Gui\Datatable\Engines;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use function Illuminate\Support\enum_value;

class EloquentEngine extends AbstractEngine
{
    /**
     * Query builder
     * @var Builder
     */
    protected Builder $query;

    /**
     * Constructor method for initializing the class with a query and an optional UID.
     *
     * @param string|Builder $query The query parameter, can be a string representing a class name or an instance of Builder.
     * @param string|null $uid Optional unique identifier for the instance.
     * @return void
     */
    public function __construct(string|Builder $query, ?string $uid = null)
    {
        $this->query = is_string($query) ? call_user_func($query . '::query') : $query;
        parent::__construct($uid);
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
                return (string) enum_value($v);
            }
            return (string) $v;
        })->flip()->flip();
    }
}