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
        $this->paginator = $this->query->paginate($perPage, ['*'], 'page', $this->currentPage, $total);
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
        return $query->get($column)->transform(function($value) use ($column){
            return (string) $value[$column];
        })->flip()->flip();
    }
}