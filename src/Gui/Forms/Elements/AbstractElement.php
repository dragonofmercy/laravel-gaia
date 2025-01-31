<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Traits\FieldName;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use Gui\Forms\Traits\Form;
use Gui\Forms\Traits\Options;
use Gui\Forms\Traits\Attributes;
use Gui\Forms\Validators\Error;

abstract class AbstractElement
{
    use Options, Attributes, Form, FieldName;

    /**
     * Constructor.
     *
     * @param Collection|array $options
     * @param Collection|array $attributes
     */
    public function __construct(Collection|array $options = [], Collection|array $attributes = [])
    {
        $this->initalizeOptions();
        $this->initalizeAttributes($attributes);

        $this->initialize();

        $this->validateOptions($options);
        $this->beforeRender();
    }

    /**
     * Function called just before render, options passed with
     * constructor are accessible at this level
     *
     * @return void
     */
    protected function beforeRender(): void
    {
    }

    /**
     * Initialize element
     *
     * @return void
     */
    protected function initialize(): void
    {
        $this->addOption('isHidden', false);
    }

    /**
     * Finalize attributes
     *
     * @param Collection $attributes
     * @return Collection
     */
    public function finalizeAttributes(Collection $attributes): Collection
    {
        if($attributes->has('name')){
            $attributes['id'] = $this->generateId($attributes->get('name'));
        }

        return $attributes;
    }

    /**
     * Check if element is hidden
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return (bool) $this->options->get('isHidden', false);
    }

    /**
     * Check if field is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->getAttribute('disabled', false) || $this->getAttribute('readonly', false);
    }

    /**
     * Generate id for form fields
     *
     * @param string $name
     * @param mixed|null $value
     * @return string
     */
    public function generateId(string $name, mixed $value = null): string
    {
        if(Str::contains($name, '[')){
            $name = str_replace(['[]', '][', '[', ']'], [(null !== $value ? '_' . Str::lower($value) : ''), '_', '_', ''], $name);
        }

        return preg_replace(['/^[^A-Za-z]+/', '/[^A-Za-z0-9:_.\-]/'], ['', '_'], $name);
    }

    /**
     * Render html tag
     *
     * @param string $tag
     * @param Collection|array $attributes
     * @return string
     */
    public function renderTag(string $tag, Collection|array $attributes = new Collection()): string
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        return tag($tag, $this->finalizeAttributes($attributes));
    }

    /**
     * Render html content tag
     *
     * @param string $tag
     * @param string $content
     * @param Collection|array $attributes
     * @return string
     */
    public function renderContentTag(string $tag, string $content = "", Collection|array $attributes = new Collection()): string
    {
        if(!$attributes instanceof Collection){
            $attributes = collect($attributes);
        }

        return content_tag($tag, $content, $this->finalizeAttributes($attributes));
    }

    /**
     *
     * @param string $haystack
     * @param array $needles
     * @return string
     */
    protected function replace(string $haystack, array $needles = []): string
    {
        return str_replace(array_keys($needles), array_values($needles), $haystack);
    }

    /**
     * Render element
     *
     * @param string $name
     * @param mixed|null $value
     * @param Error|null $error
     * @return string
     */
    abstract public function render(string $name, mixed $value = null, ?Error $error = null): string;
}