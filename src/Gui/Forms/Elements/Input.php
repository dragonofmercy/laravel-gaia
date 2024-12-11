<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

abstract class Input extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('type');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        $this->setAttribute('name', $name);
        $this->setAttribute('value', $value);

        if(!$this->hasAttribute('type')){
            $this->setAttribute('type', $this->getOption('type'));
        }

        return $this->renderTag('input', $this->attributes);
    }
}