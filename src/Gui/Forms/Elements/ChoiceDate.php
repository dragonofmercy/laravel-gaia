<?php
namespace Gui\Forms\Elements;

use Carbon\Carbon;
use Demeter\Support\Str;
use Gui\Forms\Validators\Error;
use Illuminate\Support\Collection;

class ChoiceDate extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('class', 'form-date');
        $this->addOption('format', '{days}{months}{years}');
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
    protected function beforeRender() : void
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
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        $replacements = [];
        $value = $this->convertValueToArray($value);

        if(preg_match_all('/{(days|months|years|hours|minutes)}/', $this->getOption('format'), $matches)){
            for($i = 0; $i < count($matches[0]); $i++){
                $replacements[$matches[0][$i]] = $this->{"getChoices" . ucfirst($matches[1][$i])}($name, $value);
            }
        }

        return content_tag('div', Str::strtr($this->getOption('format'), $replacements), ['class' => $this->getOption('class')]);
    }

    /**
     * Convert value to array of values
     *
     * @param mixed $value
     * @return array
     */
    protected function convertValueToArray(mixed $value) : array
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
     * Render element
     *
     * @param string $name
     * @param string|int|null $value
     * @param array $options
     * @param Collection $attributes
     * @return string
     */
    protected function renderElement(string $name, string|int|null $value = null, array $options = [], Collection $attributes = new Collection()) : string
    {
        $options['addEmpty'] = $this->getOption('addEmpty');
        return (new ChoiceSelect($options, $attributes))->render($name, $value);
    }

    /**
     * Get days combobox
     *
     * @param string $name
     * @param array $v
     * @return string
     */
    protected function getChoicesDays(string $name, array $v) : string
    {
        $choices = [];
        $definitions = $this->getOption('days');

        for($i = $definitions[0]; $i <= $definitions[1]; $i++){
            $choices[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }

        return $this->renderElement($name . '[day]', $v['day'], ['choices' => $choices]);
    }

    /**
     * Get months combobox
     *
     * @param string $name
     * @param array $v
     * @return string
     */
    protected function getChoicesMonths(string $name, array $v) : string
    {
        $choices = [];
        $definitions = $this->getOption('months');

        for($i = $definitions[0]; $i <= $definitions[1]; $i++){
            $choices[sprintf('%02d', $i)] = Carbon::parse('1970-' . $i . '-01')->locale($this->getOption('locale'))->isoFormat($this->getOption('monthIsoFormat'));
        }

        return $this->renderElement($name . '[month]', $v['month'], ['choices' => $choices]);
    }

    /**
     * Get years combobox
     *
     * @param string $name
     * @param array $v
     * @return string
     */
    protected function getChoicesYears(string $name, array $v) : string
    {
        $choices = [];
        $definitions = $this->getOption('years');

        for($i = $definitions[0]; $i <= $definitions[1]; $i++){
            $choices[$i] = $i;
        }

        return $this->renderElement($name . '[year]', $v['year'], ['choices' => $choices]);
    }

    /**
     * Get years combobox
     *
     * @param string $name
     * @param array $v
     * @return string
     */
    protected function getChoicesHours(string $name, array $v) : string
    {
        $choices = [];
        $definitions = $this->getOption('hours');

        for($i = $definitions[0]; $i <= $definitions[1]; $i++){
            $choices[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }

        return $this->renderElement($name . '[hour]', $v['hour'], ['choices' => $choices]);
    }

    /**
     * Get years combobox
     *
     * @param string $name
     * @param array $v
     * @return string
     */
    protected function getChoicesMinutes(string $name, array $v) : string
    {
        $choices = [];
        $definitions = $this->getOption('minutes');

        for($i = $definitions[0]; $i <= $definitions[1]; $i++){
            $choices[sprintf('%02d', $i)] = sprintf('%02d', $i);
        }

        return $this->renderElement($name . '[minute]', $v['minute'], ['choices' => $choices]);
    }
}