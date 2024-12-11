<?php
namespace Gui\Datatable\Engines;

use Closure;
use Demeter\Support\Str;
use Gui\Datatable\Decorators\AbstractDecorator;
use Gui\Datatable\Decorators\DefaultDecorator;
use Gui\Datatable\Filters\AbstractFilter;
use Gui\Datatable\Options;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request as RequestFacade;

abstract class AbstractEngine
{
    const SORT_DIRECTION_ASC = 'asc';
    const SORT_DIRECTION_DESC = 'desc';
    const SESSION_STORAGE_KEY = "datatables.persistance";

    /**
     * Default datatable decorator
     * @var string
     */
    public static string $defaultDecorator = DefaultDecorator::class;

    /**
     * Datatable UID
     * @var string|mixed
     */
    protected string $uid;

    /**
     * Columns collection
     * @var Collection<string, string>
     */
    protected Collection $columns;

    /**
     * Column options collection
     * @var Collection<string, array>
     */
    protected Collection $columnsOptions;

    /**
     * Search filter
     *
     * @var Collection<string, AbstractFilter>
     */
    protected Collection $searchFilters;

    /**
     * Search values collection
     * @var Collection<string, mixed>
     */
    protected Collection $searchValues;

    /**
     * Datatable options collection
     * @var Collection<string, mixed>
     */
    protected Collection $options;

    /**
     * Decorator classname
     * @var string
     */
    protected string $decorator;

    /**
     * Current page
     * @var int
     */
    protected int $currentPage = 1;

    /**
     * Sort by
     * @var string|null
     */
    protected string|null $sortBy;

    /**
     * Sort direction
     * @var string|null
     */
    protected string|null $sortDirection;

    /**
     * Paginator instance
     * @var LengthAwarePaginator
     */
    protected LengthAwarePaginator $paginator;

    /**
     * Row format closure or callable
     * @var Closure
     */
    protected Closure $rowFormat;

    /**
     * Datatable built flag
     * @var bool
     */
    protected bool $built = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->columns = new Collection();
        $this->columnsOptions = new Collection();
        $this->searchFilters = new Collection();
        $this->searchValues = new Collection();
        $this->options = new Collection([
            'display_search' => false,
            'default_sort_by' => null,
            'default_sort_order' => 'null',
            'row_limit' => 25,
        ]);

        $this->uid = RequestFacade::get('dt_u');
        $this->currentPage = RequestFacade::get('dt_p', 1);
        $this->sortBy = RequestFacade::get('dt_s');
        $this->sortDirection = RequestFacade::get('dt_o');

        $this->initializeSearchValues();
    }

    /**
     * Get the number of columns
     *
     * @return int
     */
    public function columnsCount() : int
    {
        return $this->columns->count();
    }

    /**
     * Set columns names
     *
     * @param Collection|array $columns
     * @param bool $reset
     * @return void
     */
    public function setColumns(Collection|array $columns, bool $reset = false) : void
    {
        if($reset){
            $this->columns = new Collection();
        }

        foreach($columns as $column => $label){
            $this->setColumn($column, $label);
        }
    }

    /**
     * Set a single column
     *
     * @param string $column
     * @param string $label
     * @return void
     */
    public function setColumn(string $column, string $label) : void
    {
        $this->columns[$column] = $label;
    }

    /**
     * Get all columns
     *
     * @return Collection
     */
    public function getColumns() : Collection
    {
        return $this->columns;
    }

    /**
     * Get column
     *
     * @param string $column
     * @return string
     */
    public function getColumn(string $column) : string
    {
        if($this->columns->has($column)){
            return $this->columns->get($column);
        }

        throw new \InvalidArgumentException("Column name [$column] was not found");
    }

    /**
     * Set all columns options
     *
     * @param Collection|array $options
     * @return void
     */
    public function setColumnsOptions(Collection|array $options) : void
    {
        foreach($options as $column => $option){
            $this->setColumnOptions($column, $option);
        }
    }

    /**
     * Set column options
     *
     * @param string $column
     * @param Options $options
     * @return void
     */
    public function setColumnOptions(string $column, Options $options) : void
    {
        $this->columnsOptions[$column] = $options;
    }

    /**
     * Get all columns options
     *
     * @return Collection<string, Options>
     */
    public function getColumnsOptions() : Collection
    {
        return $this->columnsOptions;
    }

    /**
     * Get column options
     *
     * @param string $column
     * @return Options
     */
    public function getColumnOptions(string $column) : Options
    {
        return $this->columnsOptions[$column] ?? Options::make();
    }

    /**
     * Get Uid
     *
     * @return string
     */
    public function getUid() : string
    {
        return $this->uid;
    }

    /**
     * Set row limit
     *
     * @param int $rowLimit
     * @return void
     */
    public function setLimit(int $rowLimit) : void
    {
        $this->options['row_limit'] = $rowLimit;
    }

    /**
     * Get row limit
     *
     * @return int
     */
    public function getLimit() : int
    {
        return (int) $this->options->get('row_limit', 25);
    }

    /**
     * Set row formatter closure
     *
     * @param callable $callable
     * @return void
     */
    public function setRowFormat(callable $callable) : void
    {
        $this->rowFormat = $callable(...);
    }

    /**
     * Set default sorting
     *
     * @param string $sortBy
     * @param string $sortOrder
     * @return void
     */
    public function setDefaultSorting(string $sortBy, string $sortOrder) : void
    {
        if(!$this->columns->has($sortBy)){
            throw new \InvalidArgumentException("The column [$sortBy] was not found");
        }

        $this->options['default_sort_by'] = $sortBy;
        $this->options['default_sort_order'] = $sortOrder;
    }

    /**
     * Get sorted by
     *
     * @return string|null
     */
    public function getSortBy() : string|null
    {
        return $this->sortBy;
    }

    /**
     * Set display search
     *
     * @param bool $value
     * @return void
     */
    public function setDisplaySearch(bool $value) : void
    {
        $this->options['display_search'] = $value;
    }

    /**
     * Get display search option
     *
     * @return bool
     */
    public function getDisplaySearch(): bool
    {
        return (bool) $this->options->get('display_search', false);
    }

    /**
     * Get if order is descending
     *
     * @return string
     */
    public function getSortDirection() : string
    {
        return $this->sortDirection ?? self::SORT_DIRECTION_ASC;
    }

    /**
     * Make the datalist from the collection
     *
     * @return $this
     */
    public function make() : AbstractEngine
    {
        $this->persistant();

        if(!isset($this->sortBy)){
            $this->sortBy = $this->options->get('default_sort_by');
        }

        if(!isset($this->sortDirection)){
            $this->sortDirection = $this->options->get('default_sort_order', self::SORT_DIRECTION_ASC);
        }

        $this->filter();

        if(!empty($this->sortBy) && !empty($this->sortDirection))
        {
            $this->sort();
        }

        if(!isset($this->paginator)){
            $this->initPaginator();
        }

        if(isset($this->rowFormat)){
            $this->paginator->getCollection()->transform($this->rowFormat);
        } else {
            $this->paginator->getCollection()->transform(function($item){
                return $this->columns->map(function($v, $k) use($item){
                    return $this->columnValue($item, $k);
                });
            });
        }

        $this->built = true;
        return $this;
    }

    /**
     * Get column value
     *
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    public function columnValue(mixed $value, string $key) : mixed
    {
        if($value instanceof Arrayable || is_array($value)){
            return $value[$key] ?? "";
        } elseif($value instanceof \stdClass) {
            return $value->$key;
        } else {
            return "";
        }
    }

    /**
     * Get paginator collection
     *
     * @return Collection
     */
    public function getPaginationCollection() : Collection
    {
        if(!$this->built){
            $this->make();
        }

        return $this->paginator->getCollection();
    }

    /**
     * Get current page
     *
     * @return int
     */
    public function getCurrentPage() : int
    {
        return $this->currentPage;
    }

    /**
     * Get pagination
     *
     * @return LengthAwarePaginator
     */
    public function getPagination() : LengthAwarePaginator
    {
        return $this->paginator;
    }

    /**
     * Update and save persistant data
     *
     * @param bool $clear
     * @return void
     */
    protected function persistant(bool $clear = false) : void
    {
        $items = $this->getDatatableAttributes();

        if(RequestFacade::get('dt_c') || $clear){
            $this->setDatatableAttributes([]);
            return;
        }

        if($items->count() > 3 && !$this->searchValues->count()){
            $this->searchValues = $items->get('page') ? collect($items->get('search', [])) : new Collection();
        }

        if($items->count() >= 4 && null === $this->sortBy && null === $this->sortDirection){
            $this->currentPage = $items->get('page', 1);
            $this->sortBy = $items->get('sort_by', null);
            $this->sortDirection = $items->get('sort_direction', null);
        } else {
            $this->setDatatableAttributes([
                'page' => $this->currentPage,
                'sort_by' => $this->sortBy,
                'sort_direction' => $this->sortDirection,
                'search' => $this->searchValues
            ]);
        }
    }

    /**
     * Get datatable attributes from session
     *
     * @return Collection
     */
    protected function getDatatableAttributes() : Collection
    {
        $persistant = collect(session(self::SESSION_STORAGE_KEY, []));
        return collect($persistant->get($this->getUid(), []));
    }

    /**
     * Set datatable attributes in session
     *
     * @param array $value
     * @return void
     */
    protected function setDatatableAttributes(array $value) : void
    {
        $persistant = collect(session(self::SESSION_STORAGE_KEY, []));
        $persistant[$this->getUid()] = $value;
        session([self::SESSION_STORAGE_KEY => $persistant]);
    }

    /**
     * Set search filters
     *
     * @param array<string, AbstractFilter> $searchFilters
     * @return void
     */
    public function setSearchFilters(array $searchFilters) : void
    {
        foreach($searchFilters as $column => $filter){
            $this->setSearchFilter($column, $filter);
        }
    }

    /**
     * Set search filter
     *
     * @param string $column
     * @param AbstractFilter $filter
     * @return void
     */
    public function setSearchFilter(string $column, AbstractFilter $filter) : void
    {
        $filter->setEngine($this);
        $filter->setColumn($column);
        $this->searchFilters[$column] = $filter;
    }

    /**
     * Get search definitions
     *
     * @param string|null $column
     * @return AbstractFilter|Collection<string, AbstractFilter>|null
     */
    public function getSearchFilters(string|null $column = null) : AbstractFilter|Collection|null
    {
        if(null === $column){
            return $this->searchFilters;
        }

        if(!$this->searchFilters->get($column)){
            throw new \InvalidArgumentException("Search filter for column [$column] was not found");
        }

        return $this->searchFilters->get($column);
    }

    /**
     * Check if datatable have search values
     *
     * @return bool
     */
    public function hasSearch() : bool
    {
        if($this->searchValues->count()){
            foreach($this->searchValues as $value){
                if(strlen($value)){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get search values
     *
     * @param string|null $column
     * @return Collection|array|string|null
     */
    public function getSearchValues(string|null $column = null) : mixed
    {
        if(null === $column){
            return $this->searchValues;
        }

        return $this->searchValues->get($column);
    }

    /**
     * Get the datatable decorator
     *
     * @param string|null $decorator
     * @return AbstractDecorator
     */
    public function getDecorator(string|null $decorator = null) : AbstractDecorator
    {
        if(null === $decorator){
            return new static::$defaultDecorator($this);
        } else {
            if(class_exists($decorator)){
                return new $decorator($this);
            } else {
                throw new \InvalidArgumentException(sprintf('The decorator [%s] was not found.', $decorator));
            }
        }
    }

    /**
     * Set decorator class
     *
     * @param string $decorator
     * @return void
     */
    public function setDecorator(string $decorator) : void
    {
        $this->decorator = $decorator;
    }

    /**
     * @param string $column
     * @param Closure|null $formatCallback
     * @return Collection<string, mixed>
     */
    public function getColumnValues(string $column, Closure|null $formatCallback = null) : Collection
    {
        if(null === $formatCallback){
            $formatCallback = function(mixed $v){ return $v; };
        }

        return $this->getRowColumnValues($column)->flip()->map(function(mixed $v, mixed $k) use (&$collection, $formatCallback){
            return $formatCallback($k);
        })->sort();
    }

    /**
     * Filter values using search values
     *
     * @return void
     */
    protected function filter() : void
    {
        $this->searchValues->map(function(mixed $value, string $column){
            if($this->columns->has($column) && null !== $value && $value !== "" && (!is_array($value) || count(array_filter($value)) != 0)){
                if($this->searchFilters->has($column)){
                    $filter = $this->searchFilters->get($column);
                    $filter->prepare()->filter($value);
                }
            }
        });
    }

    /**
     * Initialize search values
     *
     * @return void
     */
    protected function initializeSearchValues() : void
    {
        collect(RequestFacade::post('dt_f'))->map(function(mixed $value, string $name){
            if(is_string($value)){
                $value = Str::cleanup($value);
                if(!Str::length($value)){
                    $value = null;
                }
            }
            $this->searchValues[$name] = $value;
        });
    }

    /**
     * Init paginator
     *
     * @return void
     */
    protected abstract function initPaginator() : void;

    /**
     * Sort values
     *
     * @return void
     */
    protected abstract function sort() : void;

    /**
     * Get all datas of column
     *
     * @param string $column
     * @return Collection<string, mixed>
     */
    protected abstract function getRowColumnValues(string $column) : Collection;
}