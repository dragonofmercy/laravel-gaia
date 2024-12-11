<?php
namespace Gui\Forms\Decorators;

class FloatingLabelsDecorator extends VerticalDecorator
{
    protected string $layoutRow = <<<EOF
<form-group class="vertical floating"><div class="control-label">{label}</div><div class="control-field">{help}<div class="{data_size}">{field}</div>{error}</div></form-group>
EOF;

    protected string $layoutRowWithoutLabels = <<<EOF
<form-group class="vertical floating"><div class="control-field">{help}<div class="{data_size}">{field}</div>{error}</div></form-group>
EOF;
}