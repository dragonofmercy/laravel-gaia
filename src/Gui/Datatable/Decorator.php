<?php
namespace Gui\Datatable;

use Gui\Datatable\Filters\AbstractFilter;
use Gui\Datatable\Filters\Decorators\AbstractDecorator;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

use Demeter\Support\Str;
use Gui\Datatable\Engines\AbstractEngine;

class Decorator
{
    public static string $sortingColumnClass = 'sorting';
    public static string $sortingWaitClass = 'sorting-waiting';
    public static string $sortingAscClass = 'sorting-asc';
    public static string $sortingDescClass = 'sorting-desc';
    public static string $sortingInactiveClass = 'sorting-inactive';

    /**
     * @var AbstractEngine The engine instance.
     */
    protected AbstractEngine $engine;

    /**
     * Constructor method to initialize the engine dependency.
     *
     * @param AbstractEngine $engine The engine instance to be used.
     * @return void
     */
    public function __construct(AbstractEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Renders the view using the decorator pattern.
     *
     * @return View The rendered view instance.
     */
    public function render(): View
    {
        $this->engine->make();

        return view($this->engine->getDecoratorView(), ['decorator' => $this]);
    }

    /**
     * Retrieves the unique identifier for the datatable.
     *
     * @return string The unique identifier of the datatable.
     */
    public function getDatatableUid(): string
    {
        return $this->engine->getUid();
    }

    /**
     * Determines whether the current dataset contains any rows.
     *
     * @return bool True if there are rows in the dataset; otherwise, false.
     */
    public function hasRows(): bool
    {
        return $this->engine->getPagination()->total() > 0;
    }

    /**
     * Retrieves the collection of rows for pagination.
     *
     * @return Collection The collection of rows.
     */
    public function getRows(): Collection
    {
        return $this->engine->getPaginationCollection();
    }

    /**
     * Retrieves the collection of columns.
     *
     * @return Collection The collection of columns.
     */
    public function getColumns(): Collection
    {
        return $this->engine->getColumns();
    }

    /**
     * Retrieves the value of a specific cell in a row and returns it as an HTML string.
     *
     * @param array|Collection $row The row data as an array or a Collection from which the cell value will be extracted.
     * @param string $column The name of the column whose value should be retrieved.
     * @return HtmlString The HTML content of the cell, or a placeholder for empty values.
     */
    public function getCell(array|Collection $row, string $column): HtmlString
    {
        return new HtmlString($row[$column] ?? trans('gui::messages.generic.empty'));
    }

    /**
     * Generates a header cell for a table column with sorting capabilities.
     *
     * @param string $column The name of the column for which the header cell is being generated.
     * @param string $label The display label for the column header.
     * @return HtmlString The HTML content for the column header, including sorting attributes if applicable.
     */
    public function getHeaderCell(string $column, string $label): HtmlString
    {
        $label = trans($label);
        $options = $this->engine->getColumnOptions($column);
        $attributes = [];

        if($options->sort() === false){
            return new HtmlString('<div class="' . static::$sortingInactiveClass. '">' . $label . '</div>');
        }

        if($this->engine->getSortBy() === $column){
            $attributes['class'] = $this->engine->getSortDirection() == AbstractEngine::SORT_DIRECTION_DESC ? static::$sortingDescClass : static::$sortingAscClass;
            $attributes['data-gui-url'] = $this->buildUrl(null, $column, $this->engine->getSortDirection() === AbstractEngine::SORT_DIRECTION_DESC ? AbstractEngine::SORT_DIRECTION_ASC : AbstractEngine::SORT_DIRECTION_DESC);
        } else {
            $attributes['class'] = static::$sortingWaitClass;
            $attributes['data-gui-url'] = $this->buildUrl(null, $column, $options->defaultSort());
        }

        $label.= $this->getHeadingIcon($attributes['class']);

        return new HtmlString('<a ' . (new ComponentAttributeBag($attributes))->toHtml() . '>' . $label . '</a>');
    }

    /**
     * Retrieves the HTML for a heading icon based on the specified CSS class.
     *
     * @param string $class The CSS class used to determine the appropriate icon.
     * @return string The rendered HTML content for the heading icon.
     */
    protected function getHeadingIcon(string $class): string
    {
        $iconName = match($class){
            static::$sortingDescClass => 'caret-down',
            static::$sortingAscClass => 'caret-up',
            default => 'caret-up-down'
        };

        return Blade::render('<x-gui::tabler-icon name="' . $iconName . '" />');
    }

    /**
     * Retrieves the pagination instance used for managing paginated data.
     *
     * @return LengthAwarePaginator The paginator instance that contains details about the pagination, including items, total count, and pagination links.
     */
    public function getPagination(): LengthAwarePaginator
    {
        return $this->engine->getPagination();
    }

    /**
     * Builds a URL with updated query parameters for pagination and sorting.
     *
     * @param int|null $page The page number to include in the URL query. Defaults to the current page if null.
     * @param string|null $sortBy The column name to sort by. Defaults to the current sort column if null.
     * @param string|null $sortDirection The sorting direction (e.g., 'asc' or 'desc'). Defaults to the current sort direction if null.
     * @return string The generated URL with the updated query parameters.
     */
    public function buildUrl(int|null $page = null, string|null $sortBy = null, string|null $sortDirection = null): string
    {
        return RequestFacade::url() . "?" . http_build_query($this->engine->buildHttpQuery($page, $sortBy, $sortDirection));
    }

    /**
     * Retrieves the attributes for a table column, including optional sorting and CSS classes.
     *
     * @param string $column The name of the column for which attributes are being generated.
     * @param string $label The display label for the column.
     * @param bool $dataColumnName Indicates whether to include the column name as a data attribute (defaults to true).
     * @return ComponentAttributeBag A collection of HTML attributes for the specified column.
     */
    public function getColumnAttributes(string $column, string $label, bool $dataColumnName = true): ComponentAttributeBag
    {
        $attributes = new Collection();
        $options = $this->engine->getColumnOptions($column);

        if($dataColumnName){
            $attributes['data-column-name'] = strip_tags(trans($label));
        }

        if($options->css()){
            $attributes['class'] = $options->css();
        }

        if($this->engine->getSortBy() === $column){
            $attributes['class'] = Str::join($attributes->get('class', ""), static::$sortingColumnClass);
        }

        return new ComponentAttributeBag($attributes->toArray());
    }

    /**
     * Builds a string containing CSS styles for table columns based on their configuration.
     *
     * @return string The generated CSS styles for column widths and minimum widths.
     */
    public function buildStyles(): string
    {
        $output = "";
        $columnIndex = 1;
        $options = $this->engine->getColumnsOptions();

        foreach($this->engine->getColumns()->keys() as $column){
            if($options->has($column)){
                $columnOptions = $options->get($column);

                if($columnOptions->width()){
                    $output.= '#' . $this->engine->getUid() . ' .table thead th:nth-of-type(' . $columnIndex . '){width:' . $columnOptions->width() . '}';
                }

                if($columnOptions->minWidth()){
                    $output.= '#' . $this->engine->getUid() . ' .table thead th:nth-of-type(' . $columnIndex . '){min-width:' . $columnOptions->minWidth() . '}';
                }
            }

            $columnIndex++;
        }

        return $output;
    }

    /**
     * Checks if any search filters are applied.
     *
     * @return bool Returns true if there are search filters applied, otherwise false.
     */
    public function hasSearchFilters(): bool
    {
        return $this->engine->getSearchFilters()->count() > 0;
    }

    /**
     * Determines if there are any search values present.
     *
     * @return bool True if there are search values available, otherwise false.
     */
    public function hasSearchValues(): bool
    {
        return $this->engine->getSearchValues()->count() > 0;
    }

    /**
     * Determines whether the search input should be displayed.
     *
     * @return bool True if the search input should be displayed, false otherwise.
     */
    public function shouldDisplaySearch(): bool
    {
        return $this->hasSearchValues() || $this->isSearchAlwaysVisible();
    }

    /**
     * Determines if the search input is always visible in the interface.
     *
     * @return bool True if the search input is always visible, otherwise false.
     */
    public function isSearchAlwaysVisible(): bool
    {
        return $this->engine->isSearchAlwaysVisible();
    }

    /**
     * Retrieves a collection of search filters transformed into a structured format.
     *
     * @return Collection A collection of search filters where each filter is transformed into
     *                    an associative collection containing its label, CSS class, and rendered HTML element.
     */
    public function getSearchFilters(): Collection
    {
        $filters = $this->engine->getSearchFilters();

        if(null === $filters){
            return new Collection();
        }

        return $filters->transform(function(AbstractFilter $filter, $column){
            $decoratorClass = $filter->getDecoratorClass();
            /** @var AbstractDecorator $decorator */
            $decorator = new $decoratorClass($column, $filter);

            return new Collection([
                'label' => $filter->label(),
                'class' => $decorator->getSearchGroupClass(),
                'element' => new HtmlString($decorator->render()),
            ]);
        });
    }
}