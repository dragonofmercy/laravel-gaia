<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;

class InputGroup extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('prefix');
        $this->addOption('suffix');
        $this->addOption('prefixClass', 'input-group-text');
        $this->addOption('suffixClass', 'input-group-text');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        if($this->hasOption('prefix') === false && $this->hasOption('suffix') === false){
            return parent::render($name, $value, $error);
        }

        $output = "";

        if($this->hasOption('prefix')){
            $output.= $this->renderAddon($name, $value, 'prefix');
        }

        $output.= '{field}';

        if($this->hasOption('suffix')){
            $output.= $this->renderAddon($name, $value, 'suffix');
        }

        return content_tag('div', Str::replace('{field}', parent::render($name, $value, $error), $output), ['class' => 'input-group no-borders']);
    }

    /**
     * Render form addon
     *
     * @param string $name
     * @param mixed $value
     * @param string $position
     * @return string
     */
    protected function renderAddon(string &$name, mixed &$value, string $position): string
    {
        $option = $this->getOption($position);
        $value = $value ?? '';

        if(!is_array($option)){
            $value = match ($position){
                'prefix' => preg_replace('/^' . preg_quote($option, '/') . '/', '', $value),
                'suffix' => preg_replace('/' . preg_quote($option, '/') . '$/', '', $value),
            };
            return content_tag('div', $option, ['class' => $this->getOption($position . 'Class')]);
        } else {
            $selectedChoice = null;
            if(is_array($value)){
                $selectedChoice = $value['prefix'] ?? ($value['suffix'] ?? null);
                $value = $value['value'];
            } else {
                foreach($option as $choice){
                    if($position === 'prefix'){
                        $pattern = '/^' . preg_quote($choice, '/') . '/';
                    } else {
                        $pattern = '/' . preg_quote($choice, '/') . '$/';
                    }
                    if(preg_match($pattern, $value, $matches)){
                        $value = preg_replace($pattern, '', $value);
                        $selectedChoice = $matches[0];
                        break;
                    }
                }
            }

            if(!Str::contains($name, '[value]')){
                $name = $name . '[value]';
            }

            $choiceName = Str::replace('[value]', '[' . $position . "]", $name);

            return (new ChoiceSelect(['choices' => $option]))->render($choiceName, $selectedChoice);
        }
    }
}