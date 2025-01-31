<?php
namespace Gui\Datatable\Decorators\Checkbox;

use Illuminate\Support\Collection;

use Gui\Datatable\Decorators\CheckboxDecorator;
use Gui\Datatable\Decorators\Default\RowsComponent as ComponentBase;

/**
 * @method CheckboxDecorator getParent()
 */
class RowsComponent extends ComponentBase
{
    /**
     * @inheritDoc
     */
    protected function renderCells(array|Collection $cells): string
    {
        if(!$cells instanceof Collection){
            $cells = collect($cells);
        }

        if($cells->count()){
            $cells['gui_selector'] = content_tag('div', $this->getParent()->getCheckbox($cells->get('gui_selector', null)), ['class' => 'gui-selector-checkbox']);
        }

        return parent::renderCells($cells);
    }
}