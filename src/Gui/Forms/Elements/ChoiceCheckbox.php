<?php

namespace Gui\Forms\Elements;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

use Gui\Forms\Validators\Error;

class ChoiceCheckbox extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('choices');

        $this->addOption('inline', false);
        $this->addOption('elementClass', 'gui-control-check');
        $this->addOption('groupClass', 'form-check');
        $this->addOption('labelClass', 'form-check-label');
        $this->addOption('inputClass', 'form-check-input');
        $this->addOption('multiple', true);
        $this->addOption('choicesAttributes', []);
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-checkbox';
    }

    /**
     * Retrieves the input type associated with this method.
     *
     * @return string The type of input as a string.
     */
    protected function getInputType(): string
    {
        return 'checkbox';
    }

    /**
     * Retrieves a collection of choices from the configured options.
     *
     * The method processes the 'choices' option by flattening its values. If any of the values
     * within the 'choices' option is iterable, it will convert it to a collection. Otherwise,
     * it preserves the key-value pairs.
     *
     * @return Collection A collection of processed choices.
     */
    protected function getChoices(): Collection
    {
        $output = new Collection();

        foreach($this->getOption('choices') as $key => $value){
            if(is_iterable($value)){
                $output = $output->merge($value);
            } else {
                $output->put($key, $value);
            }
        }

        return $output;
    }

    /**
     * Retrieves the attributes for the choices based on the given name and value.
     *
     * @param string $name The name attribute for the input elements.
     * @param mixed $value The value(s) to be used for determining attributes such as 'checked'.
     * @return \Illuminate\Support\Collection A collection of choice attributes mapped with keys.
     */
    protected function getChoicesAttributes(string $name, mixed $value): Collection
    {
        $choicesAttributes = collect($this->getOption('choicesAttributes'));

        return $this->getChoices()->mapWithKeys(function($choiceLabel, $choiceValue) use ($name, $value, $choicesAttributes){
            $attributes = $choicesAttributes->has($choiceValue) ? $choicesAttributes[$choiceValue] : [];

            if($attributes instanceof Arrayable){
                $attributes = $attributes->toArray();
            }

            if(null !== $value){
                if(is_array($value)){
                    if(in_array($choiceValue, $value)){
                        $attributes['checked'] = 'checked';
                    }
                } else {
                    if((string) $choiceValue == $value){
                        $attributes['checked'] = 'checked';
                    }
                }
            }

            if(array_key_exists('readonly', $attributes) && ($attributes['readonly'] === true || $attributes['readonly'] === 'readonly')){
                $attributes['onclick'] = 'return false;';
            }

            $attributes['value'] = $choiceValue;
            $attributes['name'] = $name;
            $attributes['type'] = $this->getInputType();
            $attributes['class'] = (array_key_exists('class', $attributes) ? $attributes['class'] . ' ' : '') . $this->getOption('inputClass');
            $attributes['id'] = $this->generateId($name, $choiceValue);

            return [$choiceValue => new ComponentAttributeBag($attributes)];
        });
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        if($this->getInputType() === 'checkbox' && $this->getOption('multiple')){
            if(!Str::endsWith($name, '[]')){
                $name.= '[]';
            }
        }

        $this->setAttribute('name', $name);

        $this->setViewVar('inline', $this->getOption('inline'));
        $this->setViewVar('elementClass', $this->getOption('elementClass'));
        $this->setViewVar('groupClass', $this->getOption('groupClass'));
        $this->setViewVar('labelClass', $this->getOption('labelClass'));
        $this->setViewVar('choices', $this->getChoices());
        $this->setViewVar('choicesAttributes', $this->getChoicesAttributes($name, $value));

        return parent::render($name, $value, $error);
    }
}