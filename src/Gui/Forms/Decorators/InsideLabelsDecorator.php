<?php
namespace Gui\Forms\Decorators;

class InsideLabelsDecorator extends VerticalDecorator
{
    protected string $layoutRow = <<<EOF
<form-group class="vertical inside"><div class="control-field">{help}<div class="{data_size}">{field}</div>{error}</div></form-group>
EOF;

    protected string $layoutRowWithoutLabels = <<<EOF
<form-group class="vertical inside"><div class="control-field">{help}<div class="{data_size}">{field}</div>{error}</div></form-group>
EOF;

    public function renderElement(string $name) : string
    {
        $this->getFormInstance()->getElement($name)->setAttribute('placeholder', $this->getFormInstance()->getLabel($name));

        return parent::renderElement($name);
    }
}