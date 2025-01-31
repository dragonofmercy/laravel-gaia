<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class ChoiceSwitch extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('label');
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        if($this->hasAttribute('readonly')){
            $this->setAttribute('onclick', 'return false;');
        }
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        $this->setAttribute('class', 'form-check-input');
        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'checkbox');
        $this->setAttribute('value', 1);

        if($value === true){
            $this->setAttribute('checked', 'checked');
        }

        $input = $this->renderTag('input', $this->attributes);

        if($this->hasOption('label')){
            $input.= content_tag('label', $this->getOption('label'), ['class' => 'form-check-label', 'for' => $this->generateId($name)]);
        }

        return content_tag('div', content_tag('div', $input, ['class' => 'form-check form-switch']), ['class' => 'gui-form-switch']);
    }
}