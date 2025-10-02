<?php

namespace Gui\Datatable;

use Gui\Datatable\Engines\AbstractEngine;
use Illuminate\Support\Collection;

class Options
{
    /**
     * Column name
     * @var string
     */
    protected string $column;

    /**
     * Options collection
     * @var Collection
     */
    protected Collection $options;

    /**
     * Instantiate class
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
        $this->options = new Collection([
            'width' => null,
            'min-width' => null,
            'css' => null,
            'sort' => true,
            'sort_default' => AbstractEngine::SORT_DIRECTION_ASC
        ]);
    }

    /**
     * Set width
     *
     * @param int|string|null $width
     * @return Options|string
     */
    public function width(int|string|null $width = null): self|string
    {
        if(null === $width){
            return (string) $this->options->get('width');
        }
        $this->options['width'] = is_string($width) ? $width : $width . "px";
        return $this;
    }

    /**
     * Set width
     *
     * @param int|string|null $minWidth
     * @return Options|string
     */
    public function minWidth(int|string|null $minWidth = null): self|string
    {
        if(null === $minWidth){
            return (string) $this->options->get('min-width');
        }
        $this->options['min-width'] = is_string($minWidth) ? $minWidth : $minWidth . "px";
        return $this;
    }

    /**
     * Set sorting
     *
     * @param bool $bool
     * @return Options|bool
     */
    public function sort(bool|null $bool = null): self|bool
    {
        if(null === $bool){
            return boolval($this->options->get('sort'));
        }
        $this->options['sort'] = $bool;
        return $this;
    }

    /**
     * Set css
     *
     * @param string|null $class
     * @return Options|string
     */
    public function css(string|null $class = null): self|string
    {
        if(null === $class){
            return (string) $this->options->get('css');
        }
        $this->options['css'] = $class;
        return $this;
    }

    /**
     * Set default sorting direction
     *
     * @param string|null $direction
     * @return Options|string
     */
    public function defaultSort(string|null $direction = null): self|string
    {
        if(null === $direction){
            return (string) $this->options->get('sort_default');
        }

        if($direction != AbstractEngine::SORT_DIRECTION_DESC && $direction != AbstractEngine::SORT_DIRECTION_ASC){
            throw new \InvalidArgumentException("Direction parameter is invalid.");
        }

        $this->options['sort_default'] = $direction;
        return $this;
    }
}