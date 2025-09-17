<?php
namespace Gui\Forms\Elements;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

use Gui\Forms\Traits\HasFieldName;
use Gui\Forms\Traits\HasFormInstance;
use Gui\Forms\Traits\HasOptions;
use Gui\Forms\Traits\HasAttributes;
use Gui\Forms\Validators\Error;

abstract class AbstractElement
{
    use HasOptions, HasAttributes, HasFormInstance, HasFieldName;

    /**
     * View vars
     * @var array
     */
    protected array $viewVars = [];

    /**
     * Element size
     * @var int
     */
    protected int $size;

    /**
     * Constructor.
     *
     * @param Collection|array $options
     * @param Collection|array $attributes
     * @param int|null $size
     */
    public function __construct(Collection|array $options = [], Collection|array $attributes = [], int|null $size = null)
    {
        $this->size = $size ?? config('gui.default_element_size', 12);

        $this->initalizeOptions();
        $this->initalizeAttributes($attributes);

        $this->initialize();
        $this->validateOptions($options);
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
     * Check if element is hidden
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->getOption('isHidden');
    }

    /**
     * Get the size value
     *
     * @return int The size value
     */
    public function getSize(): int
    {
        return $this->size;
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

        return Str::snake($name);
    }

    /**
     * Convert form field to an HTML string
     *
     * @param string $name The name attribute of the field
     * @param mixed|null $value The value of the field
     * @param Error|null $error Optional error associated with the field
     * @param bool $autoId Whether to automatically generate an id for the field
     * @return HtmlString The rendered HTML string of the form field
     */
    public function toHtml(string $name, mixed $value = null, ?Error $error = null, bool $autoId = true): HtmlString
    {
        if($autoId){
            $this->setAttribute('id', $this->generateId($name));
        }

        $this->beforeRender();
        return new HtmlString($this->render($name, $value, $error));
    }

    /**
     * Renders the view for the specified form element.
     *
     * @param string $name The name of the form element.
     * @param mixed|null $value The value of the form element.
     * @param Error|null $error An optional error object associated with the form element.
     * @return string|View The rendered view or string representation of the form element.
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        return view($this->getView(), [
                'attr' => new ComponentAttributeBag($this->attributes->toArray()),
                ...$this->getViewVars()
            ]
        );
    }

    /**
     * Retrieves the view variables.
     *
     * @return array An associative array containing the view variables.
     */
    protected function getViewVars(): array
    {
        return $this->viewVars;
    }

    /**
     * Set view variables
     *
     * @param array|Collection $viewVars An array or collection containing key-value pairs for view variables
     * @return void
     */
    protected function setViewVars(array|Collection $viewVars): void
    {
        foreach($viewVars as $k => $v){
            $this->setViewVar($k, $v);
        }
    }

    /**
     * Sets a single view variable with the specified name and value.
     *
     * @param string $name The name of the variable to set.
     * @param mixed $value The value to be assigned to the variable.
     * @return void
     */
    protected function setViewVar(string $name, mixed $value): void
    {
        $this->viewVars[$name] = $value;
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
     * Retrieve the view name
     *
     * @return string
     */
    abstract protected function getView(): string;
}