<?php
namespace Gui\Datatable\Filters\Decorators;

use Gui\Forms\Elements\InputDate;

class DateDecorator extends AbstractDecorator
{
    /**
     * @inheritDoc
     */
    protected string $searchGroupClass = "search-date";

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $engine = $this->filter->getEngine();
        return (new InputDate())->toHtml($this->formatFieldName($this->name), $engine->getSearchValues($this->name));
    }
}