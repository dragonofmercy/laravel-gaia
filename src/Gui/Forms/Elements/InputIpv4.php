<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class InputIpv4 extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->appendAttribute('class', 'form-control gui-control-ipv4');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        $matrix = [null, null, null, null];
        $inputs = [];

        $id = $this->generateId($name);
        $input = new InputText(attributes: [
            'class' => 'ip-part', 'maxlength' => 3,
            'size' => 3, 'inputmode' => 'numeric'
        ]);
        $input->shiftAttribute('class', 'form-control');

        if(is_array($value)){
            $matrix = array_replace($matrix, $value);
        } elseif(!empty((string) $value)) {
            $matrix = explode('.', (string) $value);
        }

        for($i = 0; $i < 4; $i++){
            $inputs[] = $input->render($name . "[" . $i . "]", $matrix[$i]);
        }

        $this->setAttribute('id', $id);

        return $this->renderContentTag('div', implode('<div class="separator">.</div>', $inputs), $this->attributes) .
            javascript_tag_deferred("$('#" . $id . "').GUIControlIpv4()");
    }
}