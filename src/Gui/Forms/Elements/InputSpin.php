<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;

class InputSpin extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('min');
        $this->addOption('max');
        $this->addOption('step', 1);
        $this->addOption('iconUp', 'fas fa-plus');
        $this->addOption('iconDown', 'fas fa-minus');
        $this->addOption('unlimitedValue', null);
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setAttribute('autocomplete', 'off');
        $this->setAttribute('inputmode', 'numeric');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        $opt = [
            'min' => $this->getOption('min'),
            'max' => $this->getOption('max'),
            'step' => $this->getOption('step'),
            'unlimitedValue' => $this->getOption('unlimitedValue')
        ];

        $field = parent::render($name, $value, $error);

        $class = Str::join('btn btn-addon', $this->isDisabled() ? 'disabled' : '');

        $buttonUp = '<a class="' . $class . '" data-type="up"><i class="' . $this->getOption('iconUp') . '"></i></a>';
        $buttonDown = '<a class="' . $class . '" data-type="down"><i class="' . $this->getOption('iconDown') . '"></i></a>';

        $js = '$("#' . $this->generateId($name) . '").GUIControlSpin(' . _javascript_php_to_object($opt) . ');';
        return content_tag('div', $buttonDown . $field . $buttonUp, ['class' => 'input-group gui-control-spin']) . javascript_tag_deferred($js);
    }
}