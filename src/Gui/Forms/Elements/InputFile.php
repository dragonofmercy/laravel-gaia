<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;

class InputFile extends Input
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->appendAttribute('class', 'form-control');

        $this->addOption('multiple', false);
        $this->addOption('button', 'gui::messages.component.file.browse');
        $this->addOption('icon', 'fa-regular fa-folder-open');
        $this->addOption('layout', '{button}{input}');
        $this->addOption('display');

        $this->setOption('type', 'file');
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

        $output = parent::render($name, null, $error);

        $buttonClass = 'btn btn-addon';
        $buttonLabel = trans($this->getOption('button'));

        if($this->getOption('icon') !== null){
            $buttonLabel = content_tag('i', '', ['class' => $this->getOption('icon')]) . $buttonLabel;
            $buttonClass = Str::join($buttonClass, ['btn-icon', 'inline']);
        }

        $button = content_tag('button', $buttonLabel, ['type' => 'button', 'class' => $buttonClass]);
        $input = tag('input', ['type' => 'text', 'class' => 'form-control', 'readonly' => 'readonly']);

        $output.= content_tag('div', Str::strtr($this->getOption('layout'), ['{button}' => $button, '{input}' => $input]), ['class' => 'input-group']);

        $opt = [];
        $opt['filenameDisplay'] = $this->getOption('display');
        $opt['strings'] = '{ multiple: "' . trans('gui::messages.component.file.multiple') . '", empty: "' . trans('gui::messages.component.file.empty') . '" }';

        return content_tag('div', $output, ['class' => 'gui-control-file']) .
            javascript_tag_deferred('$("#' . $this->generateId($name) . '").GUIControlFile(' . _javascript_php_to_object($opt) . ')');
    }
}