<?php
namespace Gui\Forms\Decorators;

use Demeter\Support\Str;
use Gui\Forms\Elements\AbstractElement;
use Gui\Forms\Elements\InputHidden;
use Gui\Forms\Form;

abstract class AbstractDecorator
{
    protected string $layoutRow = <<<EOF
<form-group><div class="control-label">{label}</div><div class="control-field">{help}<div class="{data_size}">{field}</div>{error}</div></form-group>
EOF;

    protected string $layoutRowWithoutLabels = <<<EOF
<form-group><div class="control-field">{help}<div class="{data_size}">{field}</div>{error}</div></form-group>
EOF;

    protected string $layoutError = <<<EOF
<div class="invalid-feedback">{error}</div>
EOF;

    protected string $layoutErrorGlobal = <<<EOF
<div class="alert alert-danger"><i class="icon"></i><div class="alert-content">{errors}</div></div>
EOF;

    protected string $layoutErrorGlobalRow = <<<EOF
<div>{error}</div>
EOF;

    protected string $layoutLabel = <<<EOF
<label for="{for}">{label}</label>
EOF;

    protected string $layoutLabelRequired = <<<EOF
<div class="required">{label}</div>
EOF;

    protected string $layoutHelp = <<<EOF
<div class="field-hint">{help}</div>
EOF;

    protected string $layoutSeparator = <<<EOF
<form-group class="form-separator">{title}{help}</form-group>
EOF;

    /**
     * Elements name format
     * @var string
     */
    protected string $nameFormat = '%s[%s]';

    /**
     * Form instance
     *
     * @var Form
     */
    protected Form $form;

    /**
     * Layout format
     * @var string
     */
    protected string $layout = '{global_errors}{hidden_fields}{content}';

    /**
     * Hidden fields rendering status
     * @var bool
     */
    protected bool $renderedHiddenFields = false;

    /**
     * Constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
        $missing = [];

        $form->getElements()->map(function(AbstractElement $e, string $k) use (&$missing){
            if(null === $this->getFormInstance()->getValidator($k)){
                $missing[] = $k;
            }
        });

        if(count($missing)){
            throw new \RuntimeException("Extra field(s): " . implode(", ", $missing));
        }
    }

    /**
     * Get error format
     *
     * @return string
     */
    public function getErrorFormat() : string
    {
        return $this->layoutError;
    }

    /**
     * Get form instance
     *
     * @return Form
     */
    public function getFormInstance() : Form
    {
        return $this->form;
    }

    /**
     * Render form
     *
     * @return string
     */
    public function render() : string
    {
        return Str::strtr($this->layout, [
            '{global_errors}' => $this->renderGlobalErrors(),
            '{hidden_fields}' => $this->renderHiddenFields(),
            '{content}' => $this->renderRows()
        ]);
    }

    /**
     * Render hidden field (add csrf field if necessary)
     *
     * @return string
     */
    public function renderHiddenFields() : string
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

        return $output;
    }

    /**
     * Render form rows
     *
     * @param ?array $onlyRows
     * @return string
     */
    public function renderRows(?array $onlyRows = null) : string
    {
        $rows = "";

        $elements = $this->getFormInstance()->getElements()->filter(function(AbstractElement $element, string $name){
            return !$element->isHidden();
        });

        foreach($elements->only($onlyRows)->keys() as $name){
            $rows.= $this->renderRow($name);
        }

        return $rows;
    }

    /**
     * Render form row
     *
     * @param string $name
     * @return string
     */
    public function renderRow(string $name) : string
    {
        $prefix = "";

        if(!$this->getFormInstance()->getElements()->has($name)){
            return "";
        }

        $layout = $this->getFormInstance()->getLabel($name) === false ? $this->layoutRowWithoutLabels : $this->layoutRow;
        $classFieldSize = 'size-' . $this->getFormInstance()->getElement($name)->getAttribute('gui-size', config('gui.form_field_default_size', 5));
        $this->getFormInstance()->getElement($name)->removeAttribute('data-size');

        if($this->getFormInstance()->getSeparator($name)){
            $prefix.= $this->renderSeparator($name);
        }

        return $prefix . $this->addErrorClass(Str::strtr($layout, [
                '{label}' => $this->renderLabel($name),
                '{field}' => $this->renderElement($name),
                '{help}' => $this->renderHelp($name),
                '{error}' => $this->renderError($name),
                '{data_size}' => $classFieldSize
            ]), null !== $this->getFormInstance()->getError($name));
    }

    /**
     * Add error class to line layout
     *
     * @param string $line
     * @param bool $hasError
     * @return string
     */
    protected function addErrorClass(string $line, bool $hasError = false) : string
    {
        if(false === $hasError){
            return $line;
        }

        if(preg_match('/<form-group class="([a-z0-9_\-.\s]+)">/Ui', $line)){
            return preg_replace('/<form-group class="([a-z0-9_\-.\s]+)">/Ui', '<form-group class="$1 has-error">', $line);
        } else {
            return Str::replace('<form-group>', '<form-group class="has-error">', $line);
        }
    }

    /**
     * Render label
     *
     * @param string $name
     * @return string
     */
    public function renderLabel(string $name) : string
    {
        $label = $this->getFormInstance()->getLabel($name);

        if($this->getFormInstance()->getValidator($name)->getOption('required')){
            $label = str_replace('{label}', $label, $this->layoutLabelRequired);
        }

        return Str::strtr($this->layoutLabel, [
            '{label}' => $label,
            '{for}' => $this->getFormInstance()->getElement($name)->generateId($this->generateName($name))
        ]);
    }

    /**
     * Render form element
     *
     * @param string $name
     * @return string
     */
    public function renderElement(string $name) : string
    {
        $element = $this->getFormInstance()->getElement($name);

        if(!$element instanceof InputHidden && null !== $this->getFormInstance()->getError($name)){
            $element->appendAttribute('class', 'is-invalid');
        }

        return $element->render(
            $this->generateName($name),
            $this->getFormInstance()->getValue($name),
            $this->getFormInstance()->getError($name)
        );
    }

    /**
     * Render separator
     *
     * @param string $name
     * @return string
     */
    public function renderSeparator(string $name) : string
    {
        $separator = $this->getFormInstance()->getSeparator($name);
        
        return Str::strtr($this->layoutSeparator, [
            '{title}' => $separator->get('title') ? content_tag('div', trans($separator->get('title')), ['class' => 'separator-title']) : "",
            '{help}' => $separator->get('help') ? content_tag('div', trans($separator->get('help')), ['class' => 'separator-help']) : ""
        ]);
    }

    /**
     * Generate element name
     *
     * @param string $name
     * @return string
     */
    public function generateName(string $name) : string
    {
        return sprintf($this->nameFormat, $this->getFormInstance()->getName(), $name);
    }

    /**
     * Render help
     *
     * @param string $name
     * @return string
     */
    public function renderHelp(string $name) : string
    {
        if(null === $this->getFormInstance()->getHelp($name)){
            return "";
        }

        return Str::strtr($this->layoutHelp, [
            '{help}' => trans($this->getFormInstance()->getHelp($name))
        ]);
    }

    /**
     * Render global errors
     *
     * @return string
     */
    public function renderGlobalErrors() : string
    {
        $output = '';

        if(!$this->getFormInstance()->getGlobalErrors()->count()){
            return $output;
        }

        foreach($this->getFormInstance()->getGlobalErrors() as $error){
            $output.= Str::strtr($this->layoutErrorGlobalRow, ['{error}' => $error->getMessage()]);
        }

        return Str::strtr($this->layoutErrorGlobal, ['{errors}' => $output]);
    }

    /**
     * Render error
     *
     * @param string $name
     * @return string
     */
    public function renderError(string $name) : string
    {
        $error = $this->getFormInstance()->getError($name);

        if(null === $error || $error->getValidator()->getFlag('global') || $error->getValidator()->getFlag('hidden')){
            return "";
        }

        return str_replace('{error}', $error->getMessage(), $this->layoutError);
    }
}