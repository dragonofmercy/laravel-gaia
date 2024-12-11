<?php
namespace Gui\Datatable\Decorators\Default\SearchTypes;

use Gui\Datatable\Decorators\Default\SearchComponent;
use Gui\Forms\Elements\InputText;

class TextSearchType extends AbstractSearchType
{
    /**
     * @inheritDoc
     */
    protected string $parentElementClass = "search-text";

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $engine = $this->getParent()->getParent()->getEngine();
        $element = new InputText([], [
            'onkeydown' => sprintf(
                $this->getParent()->getJavascriptFunction(SearchComponent::SEARCH_FUNCTION_KEYDOWN),
                $engine->getUid(),
                url($this->getParent()->getParent()->url(1))
            )
        ]);

        return $element->render($this->getParent()->formatFieldName($this->name), $engine->getSearchValues($this->name));
    }
}