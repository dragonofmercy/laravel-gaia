<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;

class ChoiceCheckbox extends AbstractElement
{
    /**
     * Input type
     * @var string
     */
    protected string $inputType = 'checkbox';

    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('choices');
        $this->addOption('class', 'gui-form-checkbox');
        $this->addOption('inputClass', 'form-check-input');
        $this->addOption('labelClass', 'form-check-label');
        $this->addOption('groupClass', 'form-check');
        $this->addOption('inline', true);
        $this->addOption('margin', true);
        $this->addOption('optionsAttributes', []);
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender() : void
    {
        parent::beforeRender();

        $this->appendAttribute('class', $this->getOption('class'));

        if(!$this->getOption('margin')){
            $this->appendAttribute('class', 'no-margin');
        }

        if($this->getOption('inline')){
            $this->appendAttribute('class', 'inline');
        }
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        return content_tag('div', $this->formatChoices($name, $value), $this->attributes);
    }

    /**
     * Format choices
     *
     * @param string $name
     * @param mixed $value
     * @return string
     */
    protected function formatChoices(string $name, mixed $value) : string
    {
        $choices = collect($this->getOption('choices'));
        $name = $this->getInputName($name, $choices->count());
        $optionsAttributes = collect($this->getOption('optionsAttributes'));
        $output = "";

        foreach($choices as $v => $label){
            $id = $this->generateId($name, (string) $v);
            $attributes = collect([
                'id' => $id,
                'name' => $name,
                'type' => $this->inputType,
                'value' => (string) $v,
                'class' => $this->getOption('inputClass')
            ]);

            if(is_array($value) ? in_array($v, $value) : (string) $v == $value){
                $attributes['checked'] = 'checked';
            }

            if($this->getAttribute('readonly')){
                $attributes['onclick'] = 'return false;';
            }

            if($optionsAttributes->has($v) && is_array($optionsAttributes->get($v))){
                collect($optionsAttributes[$v])->map(function(mixed $attributesValue, string $attributeKey) use (&$attributes){
                    if($attributes->has($attributeKey)){
                        $attributes[$attributeKey] = Str::join($attributes->get($attributeKey), (string) $attributesValue, ' ');
                    } else {
                        $attributes[$attributeKey] = (string) $attributesValue;
                    }
                });
            }

            $label = !empty($label) ? content_tag('label', trans($label), ['class' => $this->getOption('labelClass'), 'for' => $id]) : "";
            $output.= content_tag('div', tag('input', $attributes) . $label, ['class' => $this->getOption('groupClass')]);
        }

        return $output;
    }

    /**
     * Get input name
     *
     * @param string $name
     * @param int $count
     * @return string
     */
    protected function getInputName(string $name, int $count) : string
    {
        if(Str::contains($name, '[') && $count > 1){
            $name.= '[]';
        }

        return $name;
    }
}