<?php

namespace Gui\Datatable\Engines;

use Illuminate\Support\Collection;

class ArrayEngine extends AbstractEngine
{
    /**
     * Collection of all data
     * @var Collection
     */
    protected Collection $collection;

    /**
     * Constructor method to initialize the object with a collection and optional unique identifier.
     *
     * @param Collection|array $collection The collection of items or an array to be converted into a collection.
     * @param string|null $uid An optional unique identifier for the object.
     * @return void
     */
    public function __construct(Collection|array $collection, ?string $uid = null)
    {
        $this->collection = $collection instanceof Collection ? $collection : Collection::make($collection);
        parent::__construct($uid);
    }

    /**
     * Filter the collection with a callable
     *
     * @param callable $callable
     * @return void
     */
    public function filterCollection(callable $callable): void
    {
        $this->collection = $this->collection->filter($callable);
    }

    /**
     * @inheritDoc
     */
    protected function initPaginator(): void
    {
        if($this->getLimit() > 0){
            $items = $this->collection->forPage($this->currentPage, $this->getLimit());
            $perPage = $this->getLimit();
        } else {
            $items = $this->collection;
            $perPage = $this->collection->count();

            if($perPage < 1){
                $perPage = 1;
            }
        }

        $this->paginator = $this->buildPaginator($items, $this->collection->count(), $perPage, $this->currentPage, []);
    }

    /**
     * @inheritDoc
     */
    protected function sort(): void
    {
        $this->collection = $this->collection->sortBy($this->sortBy, SORT_REGULAR, $this->sortDirection === self::SORT_DIRECTION_DESC);
    }

    /**
     * @inheritDoc
     */
    protected function getRowColumnValues(string $column): Collection
    {
        $collection = clone $this->collection;
        return $collection->transform(function(mixed $value) use ($column){
            $v = $value[$column];
            if($v instanceof \UnitEnum){
                return (string) $v->value;
            }
            return (string) $v;
        })->flip()->flip();
    }
}