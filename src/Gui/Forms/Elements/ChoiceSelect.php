<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;
use Illuminate\Support\Collection;
use Laminas\Escaper\Escaper;

class ChoiceSelect extends AbstractElement
{
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
        $this->addOption('featured', []);
        $this->addOption('featuredSeparator', '----------');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        if($this->getOption('multiple')){
            $this->setAttribute('multiple', 'multiple');
            if(!Str::endsWith($name, '[]')){
                $name.= '[]';
            }
        }

        $this->setAttribute('name', $name);

        return $this->renderContentTag('select', $this->renderOptions($this->getChoices(), $value), $this->attributes);
    }

    /**
     * Render options
     *
     * @param Collection|array $choices
     * @param mixed|null $value
     * @return string
     */
    protected function renderOptions(Collection|array $choices = [], mixed $value = null): string
    {
        $output = "";
        $optionsAttributes = collect($this->getOption('optionsAttributes'));

        if(is_array($choices)){
            $choices = collect($choices);
        }

        if(!is_array($value)){
            $value = [$value];
        }

        $value = array_map('strval', array_values($value));
        $valuesSet = array_flip($value);

        $choices->map(function(mixed $optValue, mixed $optKey) use (&$output, $valuesSet, $value, $optionsAttributes){
            if(is_array($optValue)){
                $output.= $this->renderContentTag('optgroup', $this->renderOptions($optValue, $value), collect(['label' => $optKey]));
            } else {
                $attributes = new Collection();
                $attributes['value'] = $optKey;

                if(isset($valuesSet[(string) $optKey])){
                    $attributes['selected'] = 'selected';
                }

                if($optionsAttributes->has($optKey) && is_array($optionsAttributes->get($optKey))){
                    collect($optionsAttributes[$optKey])->map(function(mixed $attributesValue, string $attributeKey) use (&$attributes){
                        if($attributes->has($attributeKey)){
                            $attributes[$attributeKey] = Str::join($attributes->get($attributeKey), (string) $attributesValue, ' ');
                        } else {
                            $attributes[$attributeKey] = (string) $attributesValue;
                        }
                    });
                }

                $output.= $this->renderContentTag('option', (new Escaper())->escapeHtml($optValue), $attributes);
            }
        });

        return $output;
    }

    /**
     * Get choices
     *
     * @return Collection
     */
    protected function getChoices(): Collection
    {
        $options = collect($this->getOption('choices'));
        $featured = collect($this->getOption('featured'))->reverse();

        if($this->getOption('addEmpty') !== false){
            $options->prepend(true === $this->getOption('addEmpty') ? " " : $this->getOption('addEmpty'), '');
        }

        if($featured->count()){
            $options->prepend([], $this->getOption('featuredSeparator'));
            $featured->map(function(string $name) use (&$options){
                if($options->has($name)){
                    $optValue = $options->get($name);
                    $options->forget($name);
                    $options->prepend($optValue, $name);
                }
            });
        }

        return $options;
    }
}