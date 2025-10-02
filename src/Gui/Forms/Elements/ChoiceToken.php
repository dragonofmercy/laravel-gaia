<?php

namespace Gui\Forms\Elements;

use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;

class ChoiceToken extends ChoiceSelect
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('delimiters', [188]);
        $this->addOption('max', null);
        $this->addOption('limit', 10);
        $this->addOption('sortable', false);
        $this->addOption('openOnFocus', false);
        $this->addOption('reference', 'self');
        $this->addOption('searchConjunction', 'and');
        $this->addOption('searchRespectWordBoundaries', false);
        $this->addOption('layoutDirection', 'row');
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.choice-token';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        if(null === $this->getOption('max') || $this->getOption('max') > 1){
            $this->setOption('multiple', true);
        }

        $componentConfig = [
            'max' => $this->getOption('max'),
            'limit' => $this->getOption('limit'),
            'sortable' => $this->getOption('sortable'),
            'reference' => $this->getOption('reference'),
            'delimiters' => $this->getOption('delimiters'),
            'openOnFocus' => $this->getOption('openOnFocus'),
            'searchConjunction' => $this->getOption('searchConjunction'),
            'searchRespectWordBoundaries' >= $this->getOption('searchRespectWordBoundaries')
        ];

        $this->appendAttribute('class', 'form-control');
        $this->appendAttribute('class', 'token-container');
        $this->appendAttribute('class', 'layout-' . $this->getOption('layoutDirection'));

        $attributes = [
            'class' => str_replace('form-select ', '', $this->getAttribute('class'))
        ];

        $this->setAttribute('class', 'd-none');

        $this->setViewVar('displayAttributes', new ComponentAttributeBag($attributes));
        $this->setViewVar('componentConfig', json_encode($componentConfig));
    }

    /**
     * @inheritDoc
     */
    protected function setViewVarsBeforeRender(mixed $value): void
    {
        $choices = $this->reorderChoices($this->getChoices(), $value);

        $this->setViewVar('options', $choices);
        $this->setViewVar('optionsAttributes', $this->getOptionsAttributes($value, $choices));
    }

    /**
     * Reorders the given choices based on the provided values if the "sortable" option is enabled.
     *
     * @param Collection $choices A collection of choices to be ordered.
     * @param mixed $values The values that determine the new order. If not an array, the original order is returned.
     * @return Collection The reordered choices collection.
     */
    protected function reorderChoices(Collection $choices, mixed $values): Collection
    {
        if(!$values || !is_array($values)){
            return $choices;
        }

        $reorderedChoices = new Collection();
        $remainingChoices = clone $choices;

        foreach($values as $value){
            if($choices->has($value)){
                $reorderedChoices->put($value, $choices->get($value));
                $remainingChoices->forget($value);
            }
        }

        return collect($reorderedChoices->toArray() + $remainingChoices->toArray());
    }
}