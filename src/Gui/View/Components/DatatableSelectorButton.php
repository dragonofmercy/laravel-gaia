<?php
namespace Gui\View\Components;

use Demeter\Support\Str;
use Illuminate\View\Component;

class DatatableSelectorButton extends Component
{
    public function __construct(
        public string $id,
        public string $url,
        public string $label = "",
        public string $icon = '',
        public string $confirm = 'false',
        public string $stringNoElements = 'gui::messages.datatable.selection_no_elements',
        public string $stringOnlyOne = 'gui::messages.datatable.selection_single',
        public string $modalSize = 'modal-sm',
        public bool   $remote = false,
        public bool   $onlyOne = false,
        public bool   $newWindow = false,
        public bool   $modal = false,
        public bool   $responsive = true,
        public array  $attr = []
    ){}

    public function render() : string
    {
        $attributes = collect($this->attr);

        if($attributes->has('confirm')){
            $this->confirm = $attributes->get('confirm');
            $attributes->forget('confirm');
        }

        if(!$attributes->has('data-modal-class')){
            $attributes['data-modal-class'] = $this->modalSize;
        }

        $jsOptions = [];
        $jsOptions[] = "'" . $this->id . "'";
        $jsOptions[] = _javascript_var_to_string($this->onlyOne);
        $jsOptions[] = _javascript_var_to_string($this->remote);
        $jsOptions[] = _javascript_php_to_object(['no_elements' => trans($this->stringNoElements), 'only_one' => trans($this->stringOnlyOne)]);
        $jsOptions[] = _javascript_var_to_string($this->confirm != "false" ? trans($this->confirm) : false);
        $jsOptions[] = _javascript_var_to_string($this->newWindow);
        $jsOptions[] = _javascript_var_to_string($this->modal);

        $attributes['onclick'] = "return gui.datagridSelection(this," . implode(',', $jsOptions) . ");";

        if($this->icon != ''){
            $this->label = _gui_label_icon($this->label, $this->icon, $this->responsive);
            $attributes['class'] = Str::join($attributes->get('class', ''), 'icon-flex');
        }

        return l($this->label, $this->url, $attributes);
    }
}