<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class Textarea extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->appendAttribute('class', 'form-control');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        $this->setAttribute('name', $name);

        return $this->renderContentTag('textarea', (string) $value, $this->attributes);
    }
}