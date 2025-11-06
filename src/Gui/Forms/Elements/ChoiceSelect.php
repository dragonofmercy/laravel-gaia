<?php

namespace Gui\Forms\Elements;

use ErrorException;
use Gui\Forms\Validators\Error;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

class ChoiceSelect extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.choice-select';
    }

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->appendAttribute('class', 'form-select');

        $this->addRequiredOption('choices');
        $this->addOption('multiple', false);
        $this->addOption('addEmpty', false);
        $this->addOption('optionsAttributes', []);
    }

    /**
     * Retrieves the list of choices with an optional empty value prepended.
     *
     * @return Collection A collection of choices.
     */
    protected function getChoices(): Collection
    {
        $choices = collect($this->getOption('choices'));

        if($this->getOption('addEmpty') !== false){
            $choices->prepend(true === $this->getOption('addEmpty') ? " " : $this->getOption('addEmpty'), '');
        }

        return $choices;
    }

    /**
     * Processes and retrieves the attributes for the options in the select field.
     * Maps the attributes to each option key, handles default values, and manages "selected" states.
     *
     * @param mixed $value The current value(s) that should be marked as selected.
     *
     * @return Collection A collection where each key corresponds to an option, and the value is a ComponentAttributeBag
     *                    containing the attributes for that option.
     */
    protected function getOptionsAttributes(mixed $value, Collection $choices): Collection
    {
        try {
            $value = collect($value)->flip();
        } catch(ErrorException){
            $value = new Collection();
        }

        $optionsAttributes = collect($this->getOption('optionsAttributes'));
        $flatmap = new Collection();

        foreach($choices as $k => $v){
            if(is_iterable($v)){
                foreach($v as $subKey => $subValue){
                    $flatmap->put($subKey, $subValue);
                }
            } else {
                $flatmap->put($k, $v);
            }
        }

        return $flatmap->mapWithKeys(function($_, $key) use ($value, $optionsAttributes){
            $attributes = $optionsAttributes->get($key, []);

            if($attributes instanceof Arrayable){
                $attributes = $attributes->toArray();
            }

            if($value->has($key)){
                $attributes['selected'] = 'selected';
            }

            $attributes['value'] = $key;

            return [$key => new ComponentAttributeBag($attributes)];
        });
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        if($this->getOption('multiple')){
            $this->setAttribute('multiple', 'multiple');
            if(!Str::endsWith($name, '[]')){
                $name.= '[]';
            }
        }

        $this->setAttribute('name', $name);
        $this->setViewVarsBeforeRender($value);

        return parent::render($name, $value, $error);
    }

    /**
     * Sets view variables required before rendering the view.
     *
     * @param mixed $value A value used to determine specific options attributes.
     * @return void
     */
    protected function setViewVarsBeforeRender(mixed $value): void
    {
        $choices = $this->getChoices();

        $this->setViewVar('options', $choices);
        $this->setViewVar('optionsAttributes', $this->getOptionsAttributes($value, $choices));
    }
}