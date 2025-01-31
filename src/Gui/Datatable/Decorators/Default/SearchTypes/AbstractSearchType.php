<?php
namespace Gui\Datatable\Decorators\Default\SearchTypes;

use Gui\Datatable\Decorators\Default\SearchComponent;
use Gui\Datatable\Filters\AbstractFilter;

abstract class AbstractSearchType
{
    /**
     * Parent search decorator component
     *
     * @var SearchComponent
     */
    protected SearchComponent $parent;

    /**
     * Search field name
     * @var string
     */
    protected string $name;

    /**
     * Search field options
     *
     * @var AbstractFilter
     */
    protected AbstractFilter $filter;

    /**
     * Class for the parent element
     * @var string
     */
    protected string $parentElementClass = "";

    /**
     * Constructor.
     *
     * @param SearchComponent $parent
     * @param string $name
     * @param AbstractFilter $filter
     * @param string $parentClass
     */
    public function __construct(SearchComponent $parent, string $name, AbstractFilter $filter, string &$parentClass)
    {
        $this->parent = $parent;
        $this->name = $name;
        $this->filter = $filter;

        $parentClass = (strlen($parentClass) ? ' ' : '') . $this->parentElementClass;
    }

    /**
     * Get filter
     *
     * @return AbstractFilter
     */
    public function getFilter(): AbstractFilter
    {
        return $this->filter;
    }

    /**
     * Get parent decorator class
     *
     * @return SearchComponent
     */
    protected function getParent(): SearchComponent
    {
        return $this->parent;
    }

    /**
     * Render component
     *
     * @return string
     */
    abstract public function render(): string;
}