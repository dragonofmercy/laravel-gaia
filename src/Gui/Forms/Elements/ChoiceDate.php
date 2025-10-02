<?php

namespace Gui\Forms\Elements;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;

use Gui\Forms\Validators\Error;

class ChoiceDate extends AbstractElement
{
    const FORMAT_TOKENS = ['days', 'months', 'years', 'hours', 'minutes'];

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('class', 'form-date');
        $this->addOption('format', '{years}{months}{days}');
        $this->addOption('locale', app()->currentLocale());
        $this->addOption('monthIsoFormat', 'MMMM');
        $this->addOption('days', [1, 31]);
        $this->addOption('months', [1, 12]);
        $this->addOption('years', [(int) date('Y') - 5, (int) date('Y') + 5]);
        $this->addOption('hours', [0, 23]);
        $this->addOption('minutes', [0, 59]);
        $this->addOption('addEmpty', false);
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.choice-date';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        if(!is_array($this->getOption('days')) || count($this->getOption('days')) !== 2){
            throw new \InvalidArgumentException("Option [days] must be an array with [first day, last day]");
        }

        if(!is_array($this->getOption('months')) || count($this->getOption('months')) !== 2){
            throw new \InvalidArgumentException("Option [months] must be an array with [first month, last month]");
        }

        if(!is_array($this->getOption('years')) || count($this->getOption('years')) !== 2){
            throw new \InvalidArgumentException("Option [years] must be an array with [first year, last year]");
        }

        if(!is_array($this->getOption('hours')) || count($this->getOption('hours')) !== 2){
            throw new \InvalidArgumentException("Option [hours] must be an array with [first hour, last hour]");
        }

        if(!is_array($this->getOption('minutes')) || count($this->getOption('minutes')) !== 2){
            throw new \InvalidArgumentException("Option [minutes] must be an array with [first minute, last minute]");
        }

        $this->appendAttribute('class', $this->getOption('class'));
    }

    /**
     * Prepares elements such as selectors, selectors attributes, and options attributes
     * based on the given field name and selected values.
     *
     * @param string $fieldName The name of the field that is mapped to the selectors.
     * @param array|null $selectedValues The selected values for corresponding selectors, if any.
     * @return void
     */
    protected function prepareElement(string $fieldName, array|null $selectedValues): void
    {
        $selectors = new Collection();
        $selectorsAttributes = new Collection();
        $optionsAttributes = new Collection();

        if(preg_match_all('/{(' . implode('|', self::FORMAT_TOKENS) . ')}/', $this->getOption('format'), $matches)){
            collect($matches[1])->map(function($name) use ($selectors, $selectorsAttributes, $optionsAttributes, $fieldName, $selectedValues){
                $selectName = substr($name, 0, -1);
                $fieldName = $fieldName . '[' . $selectName . ']';
                $selectorsAttributes[$selectName] = new ComponentAttributeBag([
                    'class' => 'form-select',
                    'name' => $fieldName,
                    'id' => $this->generateId($fieldName, $selectName)
                ]);

                $selectors[$selectName] = $this->getChoices($name)->mapWithKeys(function($label, $value) use ($selectName, $optionsAttributes, $selectedValues){
                    if(!$optionsAttributes->has($selectName)){
                        $optionsAttributes[$selectName] = new Collection();
                    }

                    $attributes = [];
                    $attributes['value'] = $value;

                    if(null !== $selectedValues && array_key_exists($selectName, $selectedValues) && $selectedValues[$selectName] == $value){
                        $attributes['selected'] = 'selected';
                    }

                    $optionsAttributes[$selectName][$value] = new ComponentAttributeBag($attributes);

                    return [$value => $label];
                });
            });
        }

        $this->setViewVar('selectors', $selectors);
        $this->setViewVar('selectorsAttributes', $selectorsAttributes);
        $this->setViewVar('optionsAttributes', $optionsAttributes);
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        $value = $this->convertValueToArray($value);
        $this->prepareElement($name, $value);

        return parent::render($name, $value, $error);
    }

    /**
     * Convert value to array of values
     *
     * @param mixed $value
     * @return array
     */
    protected function convertValueToArray(mixed $value): array
    {
        $defaultValue = ['year' => null, 'month' => null, 'day' => null, 'hour' => null, 'minute' => null];

        if(is_array($value)){
            $value = array_merge($defaultValue, $value);
        } else {
            if(null === $value){
                return $defaultValue;
            }

            $c = Carbon::parse($value);
            $value = array_merge($defaultValue, [
                'year' => $c->format('Y'),
                'month' => $c->format('m'),
                'day' => $c->format('d'),
                'hour' => $c->format('H'),
                'minute' => $c->format('i')
            ]);
        }

        return $value;
    }

    /**
     * Get choices for a specific type (days, months, years, hours, minutes)
     *
     * @param string $type Type of choices to generate
     * @return Collection
     */
    protected function getChoices(string $type): Collection
    {
        $choices = new Collection();
        $optionKey = $type;
        $definitions = $this->getOption($optionKey);

        for($i = $definitions[0]; $i <= $definitions[1]; $i++){
            switch($type){
                case 'months':
                    $key = sprintf('%02d', $i);
                    $value = Carbon::parse('1970-' . $i . '-01')
                        ->locale($this->getOption('locale'))
                        ->isoFormat($this->getOption('monthIsoFormat'));
                    break;

                case 'years':
                    $key = $i;
                    $value = $i;
                    break;

                default: // days, hours, minutes
                    $key = sprintf('%02d', $i);
                    $value = sprintf('%02d', $i);
                    break;
            }

            $choices[$key] = $value;
        }

        return $choices;
    }

}