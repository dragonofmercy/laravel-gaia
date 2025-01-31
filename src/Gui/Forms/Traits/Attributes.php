<?php
namespace Gui\Forms\Traits;

use Illuminate\Support\Collection;

trait Attributes
{
    /**
     * Message collection
     * @var Collection<string, string>
     */
    protected Collection $attributes;

    /**
     * Initialize attributes
     *
     * @param Collection|array $attributes
     * @return void
     */
    public function initalizeAttributes(Collection|array $attributes = []): void
    {
        $this->attributes = new Collection($attributes);
    }

    /**
     * Set attribute
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setAttribute(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Get attributes
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes->get($name, $default);
    }

    /**
     * Check if attribute exists
     *
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return $this->attributes->get($name) !== null;
    }

    /**
     * Get attribute converted to array with separator
     *
     * @param string $name
     * @param array $default
     * @return array
     */
    public function getAttributeArray(string $name, array $default = []): array
    {
        if(!$this->attributes->has($name)){
            return $default;
        }

        return explode(" ", (string) $this->attributes->get($name));
    }

    /**
     * Append attribute to existing attribute string
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function appendAttribute(string $name, string $value): void
    {
        $attributes = $this->getAttributeArray($name);
        $attributes[] = $value;

        $this->attributes[$name] = implode(" ", $attributes);
    }

    /**
     * Shift an attribute item
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function shiftAttribute(string $name, string $value): void
    {
        $this->attributes[$name] = implode(" ", array_filter($this->getAttributeArray($name), function(string $filter) use ($value){
            return $filter != $value;
        }));
    }

    /**
     * Remove attribute
     *
     * @param string $name
     * @return void
     */
    public function removeAttribute(string $name): void
    {
        $this->attributes->forget($name);
    }

    /**
     * Get all attributes
     *
     * @return Collection
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }
}