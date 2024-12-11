<?php
namespace Gui\Forms\Traits;

use Illuminate\Support\Collection;

trait Flags
{
    /**
     * Message collection
     * @var Collection<string, mixed>
     */
    protected Collection $flags;

    /**
     * Initialize flags
     *
     * @param Collection|array $flags
     * @return void
     */
    public function initalizeFlags(Collection|array $flags = []) : void
    {
        $this->flags = collect($flags);
    }

    /**
     * Set attribute
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setFlag(string $name, mixed $value = true) : void
    {
        $this->flags[$name] = $value;
    }

    /**
     * Get if flag exists
     *
     * @param string $name
     * @return bool
     */
    public function hasFlag(string $name) : bool
    {
        return $this->flags->has($name);
    }

    /**
     * Get attributes
     *
     * @param string $name
     * @return mixed
     */
    public function getFlag(string $name) : mixed
    {
        return $this->flags->get($name);
    }
}