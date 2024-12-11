<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class InputAutocomplete extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('url');
        $this->addOption('minLength', 1);
        $this->addOption('valueField', 'value');
        $this->addOption('textField', 'text');
        $this->addOption('limit', 10);
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender() : void
    {
        parent::beforeRender();

        $this->setAttribute('autocomplete', 'off');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        return parent::render($name, $value, $error) . javascript_tag_deferred($this->getJavascript($this->generateId($name)));
    }

    /**
     * Get javascript for autocomplete
     *
     * @param string $id
     * @return string
     */
    protected function getJavascript(string $id) : string
    {
        $opt = [
            'url' => url($this->getOption('url')),
            'minLength' => $this->getOption('minLength'),
            'valueField' => $this->getOption('valueField'),
            'textField' => $this->getOption('textField'),
            'limit' => $this->getOption('limit')
        ];

        return '$("#' . $id . '").GUIControlAutocomplete(' . _javascript_php_to_object($opt) . ');';
    }
}