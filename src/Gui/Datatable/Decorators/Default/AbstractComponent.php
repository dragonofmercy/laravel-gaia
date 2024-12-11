<?php
namespace Gui\Datatable\Decorators\Default;

use Gui\Datatable\Decorators\AbstractDecorator;

abstract class AbstractComponent
{
    /**
     * Parent decorator
     *
     * @var AbstractDecorator
     */
    protected AbstractDecorator $parent;

    /**
     * Constructor.
     *
     * @param AbstractDecorator $decorator
     */
    public function __construct(AbstractDecorator $decorator)
    {
        $this->parent = $decorator;
    }

    /**
     * Get parent decorator
     *
     * @return AbstractDecorator
     */
    public function getParent() : AbstractDecorator
    {
        return $this->parent;
    }

    /**
     * Render component
     *
     * @return string
     */
    public abstract function render() : string;
}