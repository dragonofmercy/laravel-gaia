<?php
namespace Gui\View\Components;

use Illuminate\View\Component;

class DatatableFilterButton extends Component
{
    public function __construct(
        public string $id,
        public string $label = 'gui::messages.datatable.filter',
        public string $icon = 'fas fa-filter',
        public bool   $responsive = true
    ){}

    public function render(): string
    {
        return '<a data-bs-toggle="collapse" href="#datagrid_search_' . $this->id . '" class="icon-flex">' . _gui_label_icon($this->label, $this->icon, $this->responsive) . '</a>';
    }
}