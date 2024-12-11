<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class InputCode extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('size', 6);
        $this->addOption('pattern', '/[0-9]/i');
        $this->addOption('inputmode', 'numeric');
        $this->addOption('onComplete', 'function(){}');
        $this->addOption('separator', [3, '-']);
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender() : void
    {
        parent::beforeRender();

        if(count($this->getOption('separator')) < 2){
            throw new \InvalidArgumentException("Option [separator] must be an array with 2 items [position, separator].");
        }

        $this->appendAttribute('class', 'gui-control-code');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        $this->setAttribute('name', $name);

        $opt = [];
        $opt['pattern'] = '|' . $this->getOption('pattern');
        $opt['onComplete'] = $this->getOption('onComplete');

        $js = "$('#" . $this->generateId($name) . "').GUIControlCode(" . _javascript_php_to_object($opt) . ")";
        return $this->renderContentTag('div', $this->build($name), $this->attributes) . javascript_tag_deferred($js);
    }

    /**
     * Build fields
     *
     * @param string $name
     * @return string
     */
    protected function build(string $name) : string
    {
        $output = "";
        $separatorOption = $this->getOption('separator');
        $separatorAttributes = [];
        if(isset($separatorOption[2])){
            $separatorAttributes['class'] = $separatorOption[2];
        }
        $separator = content_tag('div', $separatorOption[1], $separatorAttributes);
        $input = new InputText(attributes: ['class' => 'form-control part', 'maxlength' => 1, 'size' => 1, 'inputmode' => $this->getOption('inputmode')]);

        for($i = 0; $i < $this->getOption('size'); $i++){
            if($i === $separatorOption[0]){
                $output.= $separator;
            }
            $output.= $input->render($name . '[' . $i . ']', null);
        }

        return $output;
    }
}