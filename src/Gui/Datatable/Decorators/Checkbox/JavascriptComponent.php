<?php
namespace Gui\Datatable\Decorators\Checkbox;

use Gui\Datatable\Decorators\Default\JavascriptComponent as BaseComponent;

class JavascriptComponent extends BaseComponent
{
    /**
     * Render javascript
     *
     * @return string
     */
    public function render(): string
    {
        $javascript = '$(".gui-selector input", "#' . $this->getParent()->getEngine()->getUid() . ' .headers").on("click change", function(){
    let $that = $(this);
    $(".gui-selector-checkbox input", "#' . $this->getParent()->getEngine()->getUid() . '").not(":disabled").each(function(){
        if(!$that.prop("checked")){
            $(this).prop("checked", false).trigger("change");
        } else {
            $(this).prop("checked", true).trigger("change");
        }
    });
});';
        $javascript.= "gui.init('#" . $this->getParent()->getEngine()->getUid() . "');";
        return javascript_tag(preg_replace('/\s+/', ' ', $javascript));
    }
}