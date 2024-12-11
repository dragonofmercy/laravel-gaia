<?php
namespace Gui\Datatable\Decorators\Default\SearchTypes;

use Gui\Datatable\Decorators\Default\SearchComponent;
use Gui\Datatable\Filters\DateFilter;
use Gui\Forms\Elements\InputDate;

/**
 * @method DateFilter getFilter()
 */
class DateSearchType extends AbstractSearchType
{
    /**
     * @inheritDoc
     */
    protected string $parentElementClass = "search-date";

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $engine = $this->getParent()->getParent()->getEngine();
        $element = new InputDate(['withTime' => $this->getFilter()->withTime()], [
            'onkeydown' => sprintf(
                $this->getParent()->getJavascriptFunction(SearchComponent::SEARCH_FUNCTION_KEYDOWN),
                $engine->getUid(),
                url($this->getParent()->getParent()->url(1))
            )
        ]);

        return $element->render($this->getParent()->formatFieldName($this->name), $engine->getSearchValues($this->name));
    }
}