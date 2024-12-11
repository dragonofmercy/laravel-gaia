<?php
namespace Gui\Datatable\Decorators\Default;

use Demeter\Facades\Flash as FlashFacade;

class FlashComponent extends AbstractComponent
{
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $name = 'datatable.' . $this->getParent()->getEngine()->getUid();

        if(FlashFacade::has($name)){
            return javascript_tag('$(".gui-alert-container[data-gui-name=notify]").html($("' . addslashes(gui_render_flash($name)) . '").html());');
        }

        return "";
    }
}