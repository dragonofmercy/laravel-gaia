<?php
namespace Gui\Datatable\Filters\Decorators;

use Gui\Forms\Elements\ChoiceSelect;
use Gui\Forms\Elements\ChoiceToken;

class ChoicesDecorator extends AbstractDecorator
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

        if($this->filter->multiple()){
            $element = new ChoiceToken([
                'choices' => $this->filter->choices()
            ]);
        } else {
            $element = new ChoiceSelect([
                'choices' => $this->filter->choices(),
                'addEmpty' => $this->filter->addEmpty(),
                'multiple' => $this->filter->multiple()
            ]);
        }

        return $element->toHtml($this->formatFieldName($this->name), $engine->getSearchValues($this->name));
    }
}