<?php

namespace Gui\Datatable\Engines;

use Closure;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\View\ComponentAttributeBag;

use Demeter\Support\Str;
use Gui\Datatable\Decorator;
use Gui\Datatable\Filters\AbstractFilter;
use Gui\Datatable\Options;

abstract class AbstractEngine
{
    const SORT_DIRECTION_ASC = 'asc';
    const SORT_DIRECTION_DESC = 'desc';
    const SESSION_STORAGE_KEY = "_gui.datatables";
    const SELECTOR_COLUMN_NAME = "gui-selector";

    const PARAM_UID = "dt_u";
    const PARAM_SEARCH = "dt_f";
    const PARAM_CLEAR = "dt_c";
    const PARAM_PAGE = "dt_p";
    const PARAM_SORT_BY = "dt_s";
    const PARAM_SORT_DIRECTION = "dt_o";

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
     * @var string|null
     */
    protected string|null $decoratorView = null;

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
            'search_always_visible' => false,
            'selector' => false,
            'selector_type' => 'checkbox',
            'default_sort_by' => null,
            'default_sort_order' => 'null',
            'row_limit' => 25,
        ]);

        $this->uid = RequestFacade::get(self::PARAM_UID);
        $this->currentPage = RequestFacade::get(self::PARAM_PAGE, 1);
        $this->sortBy = RequestFacade::get(self::PARAM_SORT_BY);
        $this->sortDirection = RequestFacade::get(self::PARAM_SORT_DIRECTION);

        $this->initializeSearchValues();
    }

    /**
     * Get the number of columns
     *
     * @return int
     */
    public function columnsCount(): int
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
    public function setColumns(Collection|array $columns, bool $reset = false): void
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
    public function setColumn(string $column, string $label): void
    {
        $this->columns[$column] = $label;
    }

    /**
     * Get all columns
     *
     * @return Collection
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    /**
     * Get column
     *
     * @param string $column
     * @return string
     */
    public function getColumn(string $column): string
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
    public function setColumnsOptions(Collection|array $options): void
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
    public function setColumnOptions(string $column, Options $options): void
    {
        $this->columnsOptions[$column] = $options;
    }

    /**
     * Get all columns options
     *
     * @return Collection<string, Options>
     */
    public function getColumnsOptions(): Collection
    {
        return $this->columnsOptions;
    }

    /**
     * Get column options
     *
     * @param string $column
     * @return Options
     */
    public function getColumnOptions(string $column): Options
    {
        return $this->columnsOptions[$column] ?? Options::make();
    }

    /**
     * Get Uid
     *
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Set row limit
     *
     * @param int $rowLimit
     * @return void
     */
    public function setLimit(int $rowLimit): void
    {
        $this->options['row_limit'] = $rowLimit;
    }

    /**
     * Get row limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return (int) $this->options->get('row_limit', 25);
    }

    /**
     * Set row formatter closure
     *
     * @param callable $callable
     * @return void
     */
    public function setRowFormat(callable $callable): void
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
    public function setDefaultSorting(string $sortBy, string $sortOrder): void
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
    public function getSortBy(): string|null
    {
        return $this->sortBy;
    }

    /**
     * Determines if the search is always visible or sets its visibility.
     *
     * @param bool|null $value The desired visibility state or null to retrieve the current state.
     * @return bool|static Returns the current visibility state if $value is null, or the current instance when setting the state.
     */
    public function isSearchAlwaysVisible(bool|null $value = null): bool|static
    {
        if(null === $value){
            return $this->options->get('search_always_visible', false);
        }

        $this->options['search_always_visible'] = $value;
        return $this;
    }

    /**
     * Get if order is descending
     *
     * @return string
     */
    public function getSortDirection(): string
    {
        return $this->sortDirection ?? self::SORT_DIRECTION_ASC;
    }

    /**
     * Generate a selector input element with specific attributes.
     *
     * @param mixed $value The value attribute for the input element. Defaults to null.
     * @return string The generated input element as a string.
     */
    public function getSelectorInput($value = null): string
    {
        $attributes = [
            'name' => $this->getUid() . '_selector',
            'type' => $this->options->get('selector_type'),
            'class' => 'form-check-input'
        ];

        if(null === $value){
            $attributes['disabled'] = 'disabled';
        } else {
            $attributes['value'] = $value;
        }

        $attributesBag = new ComponentAttributeBag($attributes);

        return '<input ' . $attributesBag . '/>';
    }

    /**
     * Make the datalist from the collection
     *
     * @return $this
     */
    public function make(): AbstractEngine
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

        if($this->options->get('selector', false)){
            $this->columns->prepend($this->options->get('selector_type') === 'checkbox' ? $this->getSelectorInput(false) : 'gui::messages.generic.empty', self::SELECTOR_COLUMN_NAME);
            $this->columnsOptions->prepend(Options::make()->css(self::SELECTOR_COLUMN_NAME)->sort(false), self::SELECTOR_COLUMN_NAME);

            $this->paginator->getCollection()->transform(function($item){
                $item[self::SELECTOR_COLUMN_NAME] = $this->getSelectorInput($item[self::SELECTOR_COLUMN_NAME]);
                return $item;
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
    public function columnValue(mixed $value, string $key): mixed
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
    public function getPaginationCollection(): Collection
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
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get pagination
     *
     * @return LengthAwarePaginator
     */
    public function getPagination(): LengthAwarePaginator
    {
        return $this->paginator;
    }

    /**
     * Set the selector to checkbox type
     *
     * @return static
     */
    public function checkboxes()
    {
        $this->options['selector'] = true;
        $this->options['selector_type'] = 'checkbox';
        return $this;
    }

    /**
     * Set the selector to radio type
     *
     * @return static
     */
    public function radio()
    {
        $this->options['selector'] = true;
        $this->options['selector_type'] = 'radio';
        return $this;
    }

    /**
     * Update and save persistant data
     *
     * @param bool $clear
     * @return void
     */
    protected function persistant(bool $clear = false): void
    {
        $items = $this->getDatatableAttributes();

        if(RequestFacade::get(self::PARAM_CLEAR) || $clear){
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
    protected function getDatatableAttributes(): Collection
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
    protected function setDatatableAttributes(array $value): void
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
    public function setSearchFilters(array $searchFilters): void
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
    public function setSearchFilter(string $column, AbstractFilter $filter): void
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
    public function getSearchFilters(string|null $column = null): AbstractFilter|Collection|null
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
    public function hasSearch(): bool
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
    public function getSearchValues(string|null $column = null): mixed
    {
        if(null === $column){
            return $this->searchValues;
        }

        return $this->searchValues->get($column);
    }

    /**
     * Set decorator view
     *
     * @param string $viewName
     * @return void
     */
    public function setDecoratorView(string $viewName): void
    {
        $this->decoratorView = $viewName;
    }

    /**
     * Get decorator view
     *
     * @return string
     */
    public function getDecoratorView(): string
    {
        if(null === $this->decoratorView){
            $this->decoratorView = config('gui.default_datatable_decorator_view', 'gui::datatables.default');
        }

        return $this->decoratorView;
    }

    /**
     * @param string $column
     * @param Closure|null $formatCallback
     * @return Collection<string, mixed>
     */
    public function getColumnValues(string $column, Closure|null $formatCallback = null): Collection
    {
        if(null === $formatCallback){
            $formatCallback = function(mixed $v){ return $v; };
        }

        return $this->getRowColumnValues($column)->flip()->map(function(mixed $v, mixed $k) use (&$collection, $formatCallback){
            return $formatCallback($k);
        })->sort();
    }

    /**
     * Render the object to an HTML view.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function toHtml(): \Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return (new Decorator($this))->render();
    }

    /**
     * Build HTTP query
     *
     * @param int|null $page The page number to include in the query. Defaults to the current page if null.
     * @param string|null $sortBy The sorting column to include in the query. Defaults to the current sorting column if null.
     * @param string|null $sortDirection The sorting direction to include in the query. Defaults to the current sorting direction if null.
     * @return array The constructed HTTP query as an associative array.
     */
    public function buildHttpQuery(int|null $page = null, string|null $sortBy = null, string|null $sortDirection = null): array
    {
        $httpQuery = $this->filterCurrentQuery();

        $httpQuery[self::PARAM_UID] = $this->getUid();
        $httpQuery[self::PARAM_PAGE] = $page ?? $this->getCurrentPage();
        $httpQuery[self::PARAM_SORT_BY] = $sortBy ?? $this->getSortBy();
        $httpQuery[self::PARAM_SORT_DIRECTION] = $sortDirection ?? $this->getSortDirection();

        return $httpQuery;
    }

    /**
     * Get clean http query
     *
     * @param string $filter
     * @return array
     */
    protected function filterCurrentQuery(string $filter = 'dt_'): array
    {
        return collect(RequestFacade::all())->filter(function(mixed $value, string $key) use ($filter){
            return !Str::startsWith($key, $filter);
        })->toArray();
    }

    /**
     * Filter values using search values
     *
     * @return void
     */
    protected function filter(): void
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
    protected function initializeSearchValues(): void
    {
        collect(RequestFacade::post(self::PARAM_SEARCH))->map(function(mixed $value, string $name){
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
     * Build a paginator instance.
     *
     * @param mixed $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function buildPaginator($items, $total, $perPage, $currentPage, $options)
    {
        $options = array_merge([
            'path' => RequestFacade::url(),
            'pageName' => self::PARAM_PAGE,
            'query' => $this->buildHttpQuery($currentPage)
        ], $options);

        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Redirects to a specified URL with an optional UID. If the UID is not provided, it will attempt to retrieve it from the request.
     *
     * @param string $url The URL to redirect to.
     * @param string|null $uid Optional unique identifier for the redirect. If null, it is retrieved from the current request.
     * @return string
     */
    public static function redirect(string $url, string|null $uid = null): string
    {
        if(null === $uid){
            if(!request()->has(self::PARAM_UID)){
                throw new \InvalidArgumentException("The uid was not found in the current request");
            }

            $uid = request()->get(self::PARAM_UID);
        }

        return Blade::render("<x-gui::datatable-redirector id=\"$uid\" url=\"$url\" />", [], true);
    }

    /**
     * Init paginator
     *
     * @return void
     */
    protected abstract function initPaginator(): void;

    /**
     * Sort values
     *
     * @return void
     */
    protected abstract function sort(): void;

    /**
     * Get all datas of column
     *
     * @param string $column
     * @return Collection<string, mixed>
     */
    protected abstract function getRowColumnValues(string $column): Collection;
}