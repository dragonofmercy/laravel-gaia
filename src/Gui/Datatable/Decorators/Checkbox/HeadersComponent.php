<?php
namespace Gui\Datatable\Decorators\Checkbox;

use Gui\Datatable\Decorators\Default\HeadersComponent as ComponentBase;

class HeadersComponent extends ComponentBase
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $output = parent::render();

        $replacements = array(
            'class="gui-selector ' . $this->classHiddenResponsive . '"' => 'class="gui-selector"',
        );

        return str_replace(array_keys($replacements), array_values($replacements), $output);
    }
}