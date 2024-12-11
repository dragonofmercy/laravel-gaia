<?php
namespace Gui\Forms\Decorators;

class VerticalBorderedDecorator extends AbstractDecorator
{
    protected string $layoutRow = <<<EOF
<form-group class="vertical bordered"><div class="control-label">{label}</div><div class="control-field">{help}<div class="{data_size}">{field}</div>{error}</div></form-group>
EOF;

    protected string $layoutRowWithoutLabels = <<<EOF
<form-group class="vertical bordered"><div class="control-field">{help}<div class="{data_size}">{field}</div>{error}</div></form-group>
EOF;

    protected string $layoutSeparator = <<<EOF
<form-group class="vertical bordered form-separator">{title}{help}</form-group>
EOF;
}