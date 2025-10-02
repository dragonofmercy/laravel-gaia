<?php

namespace Gui\Datatable\Filters\Decorators;

use Gui\Forms\Elements\InputText;

class TextDecorator extends AbstractDecorator
{
    /**
     * @inheritDoc
     */
    protected string $searchGroupClass = "search-text";

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $engine = $this->filter->getEngine();
        return (new InputText(attributes: $this->filter->attributes()))->toHtml($this->formatFieldName($this->name), $engine->getSearchValues($this->name));
    }
}