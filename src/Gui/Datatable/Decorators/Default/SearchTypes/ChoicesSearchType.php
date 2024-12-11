<?php
namespace Gui\Datatable\Decorators\Default\SearchTypes;

use Gui\Datatable\Filters\ChoicesFilter;
use Gui\Forms\Elements\ChoiceSelect;
use Gui\Forms\Elements\ChoiceToken;

/**
 * @method ChoicesFilter getFilter()
 */
class ChoicesSearchType extends AbstractSearchType
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

        if($this->getFilter()->multiple()){
            $element = new ChoiceToken([
                'choices' => $this->getFilter()->choices()
            ]);
        } else {
            $element = new ChoiceSelect([
                'choices' => $this->getFilter()->choices(),
                'addEmpty' => $this->getFilter()->addEmpty()
            ]);
        }


        return $element->render($this->getParent()->formatFieldName($this->name), $engine->getSearchValues($this->name));
    }
}