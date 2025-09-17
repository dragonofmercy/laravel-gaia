<?php
namespace Gui\Forms;

use Gui\Forms\Validators\Error;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;

use Gui\Forms\Elements\AbstractElement;
use Gui\Forms\Elements\InputHidden;

class Decorator
{
    /**
     * Form instance
     * @var Form
     */
    protected Form $form;

    /**
     * Elements name format
     * @var string
     */
    protected string $nameFormat = '%s[%s]';

    /**
     * Hidden fields rendering status
     * @var bool
     */
    protected bool $renderedHiddenFields = false;

    /**
     * Constructor for initializing the class with a form instance.
     *
     * @param Form $form The form instance to be utilized within the class.
     * @return void
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * Retrieves the form instance associated with the current context.
     *
     * @return Form The form instance object.
     */
    public function getFormInstance(): Form
    {
        return $this->form;
    }

    /**
     * Retrieves a collection of renderable form elements.
     *
     * Filters and returns form elements that are not hidden.
     *
     * @return Collection<AbstractElement> A collection of form elements that are not hidden.
     */
    public function getRenderableElements(): Collection
    {
        return $this->getFormInstance()->getElements()->filter(function(AbstractElement $element, string $name){
            return !$element->isHidden();
        });
    }

    /**
     * Renders the hidden fields of the form, including CSRF token if applicable.
     *
     * @return HtmlString|string The concatenated HTML output of all hidden fields and the CSRF field (if present).
     */
    public function renderHiddenFields(): HtmlString|string
    {
        $output = "";

        if($this->renderedHiddenFields){
            return $output;
        }

        $this->getFormInstance()->getElements()->map(function(AbstractElement $element, string $name) use (&$output){
            if($element->isHidden()){
                $output.= $this->renderElement($name);
            }
        });

        if(csrf_token()){
            $output.= csrf_field();
        }

        $this->renderedHiddenFields = true;

        return new HtmlString($output);
    }

    /**
     * Generates a unique identifier for a form element by its name.
     *
     * @param string $name The name of the form element for which the ID is generated.
     * @return string The generated unique identifier for the form element.
     */
    public function generateId(string $name): string
    {
        return $this->getFormInstance()->getElement($name)->generateId($this->generateName($name));
    }

    /**
     * Checks if a form element has an associated label.
     *
     * @param string $name The name of the form element to check for a label.
     * @return bool True if the form element has a label, false otherwise.
     */
    public function hasLabel(string $name): bool
    {
        return null !== $this->getFormInstance()->getLabel($name);
    }

    /**
     * Generates a label for a form element by its name.
     *
     * @param string $name The name of the form element for which the label needs to be generated.
     * @return HtmlString|null The generated label for the specified form element.
     */
    public function generateLabel(string $name): HtmlString|null
    {
        return $this->getFormInstance()->getLabel($name);
    }

    /**
     * Determines if a form field is required based on its validation rules.
     *
     * @param string $name The name of the form field to check.
     * @return bool True if the field is required, false otherwise.
     */
    public function isRequired(string $name): bool
    {
        return $this->getFormInstance()->getValidator($name)?->getOption('required') ?? false;
    }

    /**
     * Generate element name
     *
     * @param string $name
     * @return string
     */
    public function generateName(string $name): string
    {
        return sprintf($this->nameFormat, $this->getFormInstance()->getName(), $name);
    }

    /**
     * Get element size
     *
     * @param string $name
     * @return int
     */
    public function getElementSize(string $name): int
    {
        return $this->getFormInstance()->getElement($name)->getSize();
    }

    /**
     * Renders a form element by its name.
     *
     * @param string $name The name of the form element to be rendered.
     * @return HtmlString The rendered HTML output of the form element.
     */
    public function renderElement(string $name): HtmlString
    {
        $element = $this->getFormInstance()->getElement($name);

        if(!$element instanceof InputHidden && null !== $this->getFormInstance()->getError($name)){
            $element->appendAttribute('class', 'is-invalid');
        }

        return $element->toHtml(
            $this->generateName($name),
            $this->getFormInstance()->getValue($name),
            $this->getFormInstance()->getError($name)
        );
    }

    /**
     * Checks if a form element has a hint message.
     *
     * @param string $name The name of the form element to check for a hint message.
     * @return bool True if the form element has a hint message, false otherwise.
     */
    public function hasHint(string $name): bool
    {
        return null !== $this->getFormInstance()->getHelp($name);
    }

    /**
     * Generates a hint message for a specific form element.
     *
     * @param string $name The name of the form element for which to generate the hint.
     * @return HtmlString The localized hint message.
     */
    public function generateHint(string $name): HtmlString
    {
        return $this->getFormInstance()->getHelp($name);
    }

    /**
     * Checks if a specified form element has a validation error.
     *
     * @param string $name The name of the form element to check for errors.
     * @return bool True if the form element has an error, false otherwise.
     */
    public function hasError(string $name): bool
    {
        $error = $this->getFormInstance()->getError($name);

        if(null === $error || $error->getValidator()->getFlag('global') || $error->getValidator()->getFlag('hidden')){
            return false;
        }

        return true;
    }

    /**
     * Renders the error message for a specific form element.
     *
     * @param string $name The name of the form element for which the error message is to be rendered.
     * @return HtmlString The error message associated with the form element.
     */
    public function renderError(string $name): HtmlString
    {
        return new HtmlString($this->getFormInstance()->getError($name)->getMessage());
    }

    /**
     * Checks if there are any global errors in the form.
     *
     * @return bool True if the form has global errors, false otherwise.
     */
    public function hasGlobalErrors(): bool
    {
        return $this->getFormInstance()->getGlobalErrors()->count() > 0;
    }

    /**
     * Checks if a separator exists for a given form element name.
     *
     * @param string $name The name of the form element to check for a separator.
     * @return bool Returns true if a separator exists, false otherwise.
     */
    public function hasSeparator(string $name): bool
    {
        return null !== $this->getFormInstance()->getSeparator($name);
    }

    /**
     * Retrieves the separator configuration for a given form element name.
     *
     * @param string $name The name of the form element whose separator is to be retrieved.
     * @return Collection|null The separator configuration as a collection.
     */
    public function getSeparator(string $name): Collection|null
    {
        return $this->getFormInstance()->getSeparator($name);
    }

    /**
     * Retrieves the global errors of the form.
     *
     * @return Collection The collection of global errors associated with the form.
     */
    public function getGlobalErrors(): Collection
    {
        return $this->getFormInstance()->getGlobalErrors()->map(function(Error $error){
            return new HtmlString($error->getMessage());
        });
    }

    /**
     * Renders the view associated with the form decorator.
     *
     * @return View The rendered view of the form decorator.
     */
    public function render(): View
    {
        return view($this->form->getDecoratorView(), ['decorator' => $this]);
    }
}