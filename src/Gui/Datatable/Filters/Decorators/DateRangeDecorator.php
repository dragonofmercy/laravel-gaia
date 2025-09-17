<?php
namespace Gui\Datatable\Filters\Decorators;

use Gui\Forms\Elements\InputDate;

class DateRangeDecorator extends AbstractDecorator
{
    /**
     * @inheritDoc
     */
    protected string $searchGroupClass = "search-date-range";

    /**
     * Element layout
     * @var string
     */
    protected string $layout = <<<EOF
<div class="d-flex gap-3 align-items-center">{from}{to}</div>
EOF;

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $engine = $this->filter->getEngine();
        $values = collect($engine->getSearchValues($this->name));
        $options = [
            'withTime' => $this->filter->withTime()
        ];

        $replacements = [
            '{from}' => (new InputDate($options))->toHtml($this->formatFieldName($this->name, 'from'), $values->get('from')),
            '{to}' => (new InputDate($options))->toHtml($this->formatFieldName($this->name, 'to'), $values->get('to'))
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->layout);
    }
}