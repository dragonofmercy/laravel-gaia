<?php

namespace Gui\Datatable\Filters;

use Gui\Datatable\Engines\AbstractEngine;
use Gui\Datatable\Engines\EloquentEngine;
use Gui\Datatable\Filters\Decorators\AbstractDecorator;
use Illuminate\Support\Collection;

abstract class AbstractFilter
{
    /**
     * Decorator classname
     * @var string
     */
    protected string $decorator = "";

    /**
     * Engine instance
     *
     * @var AbstractEngine
     */
    protected AbstractEngine $engine;

    /**
     * Filtering column name
     * @var string
     */
    protected string $column;

    /**
     * Filter options
     * @var Collection
     */
    protected Collection $options;

    /**
     * Filter default operator
     * @var string
     */
    protected string $defaultOperator = "LIKE";

    /**
     * Instantiate filter
     *
     * @return static
     */
    public static function make(): static
    {
        return (new static);
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = new Collection();
    }

    /**
     * Get decorator classname
     *
     * @return string
     */
    public function getDecoratorClass(): string
    {
        if(!is_subclass_of($this->decorator, AbstractDecorator::class)){
            throw new \RuntimeException("Decorator class is not a subclass of [" . AbstractDecorator::class . "]");
        }

        return $this->decorator;
    }

    /**
     * Set engine to filter
     *
     * @param AbstractEngine $engine
     * @return void
     */
    public function setEngine(AbstractEngine $engine): void
    {
        $this->engine = $engine;
    }

    /**
     * Retrieve the engine
     *
     * @return AbstractEngine
     */
    public function getEngine(): AbstractEngine
    {
        return $this->engine;
    }

    /**
     * Set column name
     *
     * @param string $column
     * @return void
     */
    public function setColumn(string $column): void
    {
        $this->column = $column;
    }

    /**
     * Get if using PgSQL
     *
     * @return bool
     */
    public function usingPgsql(): bool
    {
        return config("database.connections." . config('database.default') . ".driver") === 'pgsql';
    }

    /**
     * Set or get label
     *
     * @param string|null $label
     * @return string|static
     */
    public function label(string|null $label = null): static|string
    {
        if(null === $label){
            if($this->options->has('label')){
                return $this->options->get('label', '');
            } else {
                if(isset($this->engine)){
                    return $this->engine->getColumn($this->column);
                } else {
                    return "";
                }
            }
        } else {
            $this->options['label'] = $label;
            return $this;
        }
    }

    /**
     * Get or set the attributes.
     *
     * @param array|null $attributes
     * @return static|array
     */
    public function attributes(array|null $attributes = null): static|array
    {
        if(null === $attributes){
            return $this->options->get('attributes', []);
        } else {
            $this->options['attributes'] = $attributes;
            return $this;
        }
    }

    /**
     * Set or get operator
     *
     * @param string|null $operator
     * @return string|static
     */
    public function operator(string|null $operator = null): static|string
    {
        if(null === $operator){
            return $this->options->get('operator', $this->defaultOperator);
        } else {
            $this->options['operator'] = $operator;
            return $this;
        }
    }

    /**
     * Prepare filter for filtering
     *
     * @return $this
     */
    public function prepare(): static
    {
        if(!$this->options->has('operator')){
            $this->options['operator'] = $this->defaultOperator;
        }

        if($this->engine instanceof EloquentEngine){
            if(!$this->usingPgsql() && $this->options->get('operator') == 'ILIKE'){
                $this->options['operator'] = 'LIKE';
            }
        }

        return $this;
    }

    /**
     * Filter value
     *
     * @param mixed $value
     * @return void
     */
    abstract public function filter(mixed $value): void;
}