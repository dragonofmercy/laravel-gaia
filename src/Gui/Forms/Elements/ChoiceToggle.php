<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class ChoiceToggle extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('right', 'gui::messages.generic.yes');
        $this->addOption('left', 'gui::messages.generic.no');
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
        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'checkbox');
        $this->setAttribute('value', 1);

        if($value === true){
            $this->setAttribute('checked', 'checked');
        }

        $output = $this->renderTag('input', $this->attributes);
        $output.= content_tag(name: 'div', attributes: ['class' => 'slider']);
        $output.= content_tag('div', trans($this->getOption('left')), ['class' => 'choice']);
        $output.= content_tag('div', trans($this->getOption('right')), ['class' => 'choice']);

        return content_tag('label', $output, ['class' => 'gui-form-toggle form-control', 'for' => $this->generateId($name) ]);
    }
}