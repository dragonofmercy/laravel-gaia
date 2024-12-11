<?php
namespace Gui\Datatable\Decorators\Default;

class JavascriptComponent extends AbstractComponent
{
    /**
     * Render javascript
     *
     * @return string
     */
    public function render(): string
    {
        return javascript_tag("gui.init('#" . $this->getParent()->getEngine()->getUid() . "');");
    }
}