<?php
namespace Demeter\Support;

use Illuminate\Support\Collection;
use Iterator as IteratorContract;

class Iterator implements IteratorContract
{
    /**
     * Internal array
     * @var array
     */
    private array $_array;

    /**
     * Internal index
     * @var int
     */
    private int $_index = 0;

    /**
     * Constructor.
     *
     * @param array|Collection $array $array
     */
    public function __construct(array|Collection $array)
    {
        if($array instanceof Collection) {
            $array = $array->all();
        }

        $this->_array = $array;
    }

    /**
     * Get the current element
     *
     * @return mixed
     */
    public function current(): mixed
    {
        $k = array_keys($this->_array);
        return $this->_array[$k[$this->_index]];
    }

    /**
     * Select next element
     *
     * @return void
     */
    public function next(): void
    {
        $this->_index++;
    }

    /**
     * Select previous element
     *
     * @return void
     */
    public function prev(): void
    {
        $this->_index--;
    }

    /**
     * Check if next element exists
     *
     * @return bool
     */
    public function hasNext(): bool
    {
        $this->next();
        $ret = $this->valid();
        $this->prev();

        return $ret;
    }

    /**
     * Get current key
     *
     * @return string|int
     */
    public function key(): string|int
    {
        $k = array_keys($this->_array);
        return $k[$this->_index];
    }

    /**
     * Check if current element is valid
     *
     * @return bool
     */
    public function valid(): bool
    {
        $k = array_keys($this->_array);
        return isset($k[$this->_index]);
    }

    /**
     * Rewind
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->_index = 0;
    }
}