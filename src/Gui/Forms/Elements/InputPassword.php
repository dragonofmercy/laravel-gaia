<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;

class InputPassword extends InputText
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('alwaysRenderEmpty', true);
        $this->addOption('display', true);
        $this->addOption('generator', false);
        $this->addOption('min', null);
        $this->addOption('max', null);
        $this->addOption('chars', null);
        $this->addOption('regex', null);
        $this->addOption('copyInField', 'auto');

        $this->setOption('type', 'password');
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        if(!$this->getAttribute('autocomplete'))
        {
            $this->setAttribute('autocomplete', 'new-password');
        }
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        $output = parent::render($name, $this->getOption('alwaysRenderEmpty') ? null : $value, $error);

        if($this->getOption('display')){
            $output.= content_tag('button', content_tag('i', attributes: ['class' => 'fa-regular fa-eye-slash']), ['type' => 'button', 'data-toggle' => 'toggle', 'class' => 'btn btn-addon']);
        }

        if($this->getOption('generator')){
            $output.= content_tag('button', content_tag('i', attributes: ['class' => 'fa-solid fa-gear']), ['type' => 'button', 'data-toggle' => 'generator', 'class' => 'btn btn-addon']);
        }

        $output = content_tag('div', $output, ['class' => 'input-group gui-control-password']);

        if($this->getOption('generator')){
            $output.= Str::strtr($this->getGeneratorTemplate(), [
                '{string.button.new}' => trans('gui::messages.component.password.new'),
                '{string.button.choose}' => trans('gui::messages.component.password.choose'),
                '{string.button.chooseandcopy}' => trans('gui::messages.component.password.choose_copy'),
            ]);
        }

        if($this->getOption('display') || $this->getOption('generator')){
            $opt = [];
            $opt['min'] = $this->getOption('min');
            $opt['max'] = $this->getOption('max');
            $opt['copyInField'] = $this->getOption('copyInField');
            $opt['strings'] = ['generatorTitle' => trans('gui::messages.component.password.title')];

            if(null !== $this->getOption('chars')){
                $opt['chars'] = $this->getOption('chars');
            }

            if(null !== $this->getOption('regex')){
                $opt['regex'] = $this->getOption('regex');
            }

            $output.= javascript_tag_deferred("$('#" . $this->generateId($name) . "').GUIControlPassword(" . _javascript_php_to_object($opt) . ");");
        }

        return $output;
    }

    /**
     * Get generator template
     *
     * @return string
     */
    protected function getGeneratorTemplate(): string
    {
        return <<<EOF
<div class="gui-password-generator-template">
    <div class="gui-password-generator">
        <input class="form-control password-display" readonly />
        <div class="password-size">
            <div class="range-min"></div>
            <input class="form-range" type="range" />
            <div class="range-max"></div>
        </div>
        <div class="control">
            <button type="button" class="btn btn-default btn-icon inline" data-toggle="random"><i class="fa-solid fa-retweet"></i>{string.button.new}</button>
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-icon inline" data-toggle="choose"><i class="fa-solid fa-circle-check"></i>{string.button.choose}</button>
                <button type="button" class="btn btn-default btn-icon inline" data-toggle="choose" data-copy="true"><i class="fa-regular fa-copy"></i>{string.button.chooseandcopy}</button>
            </div>
        </div>
    </div>
</div>
EOF;

    }
}