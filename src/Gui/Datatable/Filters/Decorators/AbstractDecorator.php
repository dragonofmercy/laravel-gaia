<?php
namespace Gui\Datatable\Filters\Decorators;

use Gui\Datatable\Engines\AbstractEngine;
use Gui\Datatable\Filters\AbstractFilter;

abstract class AbstractDecorator
{
    /**
     * Search group class
     * @var string
     */
    protected string $searchGroupClass = "";

    /**
     * Filter object
     * @var AbstractFilter
     */
    protected AbstractFilter $filter;

    /**
     * Filter name
     * @var string
     */
    protected string $name;

    /**
     * Constructor method
     *
     * @param string $name The name of the filter
     * @param AbstractFilter $filter The filter object
     */
    public function __construct(string $name, AbstractFilter $filter)
    {
        $this->name = $name;
        $this->filter = $filter;
    }

    /**
     * Format the field name with a specific prefix and structure.
     *
     * @param mixed ...$name Parts of the field name to format
     * @return string The formatted field name
     */
    public function formatFieldName(...$name): string
    {
        return vsprintf(AbstractEngine::PARAM_SEARCH . str_repeat('[%s]', count($name)), $name);
    }

    /**
     * Get the search group class
     *
     * @return string
     */
    public function getSearchGroupClass(): string
    {
        return $this->searchGroupClass;
    }

    /**
     * Render component
     *
     * @return string
     */
    abstract public function render(): string;
}