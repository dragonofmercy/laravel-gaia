<?php
namespace Gui\Datatable\Decorators;

use Gui\Datatable\Engines\AbstractEngine;
use Gui\Datatable\Options;

class CheckboxDecorator extends DefaultDecorator
{
    /**
     * @inheritDoc
     */
    public function __construct(AbstractEngine $engine)
    {
        $engine->setColumns($engine->getColumns()->prepend($this->getCheckbox(false), 'gui_selector'), true);
        $engine->setColumnsOptions($engine->getColumnsOptions()->prepend(Options::make()->css('gui-selector')->sort(false), 'gui_selector'));

        parent::__construct($engine);
    }

    /**
     * Get checkbox
     *
     * @param mixed|null $value null to disable, false to not set a value and gui-selector-hidden to hide
     * @return string
     */
    public function getCheckbox(mixed $value = null): string
    {
        if($value == 'gui-selector-hidden'){
            return "";
        }

        $attributes = ['type' => 'checkbox', 'class' => 'form-check-input'];

        if(null === $value){
            $attributes['disabled'] = 'disabled';
        } elseif($value !== false){
            $attributes['value'] = (string) $value;
        }

        return content_tag('div', tag('input', $attributes), ['class' => 'form-check']);
    }

    /**
     * @inheritDoc
     */
    protected function getComponents(): array
    {
        $components = parent::getComponents();
        $components['{headers}'] = Checkbox\HeadersComponent::class;
        $components['{rows}'] = Checkbox\RowsComponent::class;
        $components['{javascript}'] = Checkbox\JavascriptComponent::class;

        return $components;
    }
}