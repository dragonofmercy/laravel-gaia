<?php
namespace Gui\Datatable\Decorators\Default\SearchTypes;

use Gui\Datatable\Decorators\Default\SearchComponent;
use Gui\Datatable\Filters\DateRangeFilter;
use Gui\Forms\Elements\InputDate;

/**
 * @method DateRangeFilter getFilter()
 */
class DateRangeSearchType extends AbstractSearchType
{
    /**
     * @inheritDoc
     */
    protected string $parentElementClass = "search-date-range";

    /**
     * Control layout
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
        $engine = $this->getParent()->getParent()->getEngine();

        $attributes = [
            'onkeydown' => sprintf(
                $this->getParent()->getJavascriptFunction(SearchComponent::SEARCH_FUNCTION_KEYDOWN),
                $engine->getUid(),
                url($this->getParent()->getParent()->url(1))
            )
        ];

        $options = [
            'withTime' => $this->getFilter()->withTime()
        ];

        $v = collect($engine->getSearchValues($this->name));

        $replacements = [
            '{from}' => (new InputDate($options, $attributes))->render($this->getParent()->formatFieldName($this->name, 'from'), $v->get('from')),
            '{to}' => (new InputDate($options, $attributes))->render($this->getParent()->formatFieldName($this->name, 'to'), $v->get('to'))
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->layout);
    }
}