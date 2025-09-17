@include('gui::forms.elements.input')
<x-gui::javascript-ready>
    $('[name="{{ $attr->get('name') }}"]').GuiControlAutocomplete({!! $componentConfig !!})
</x-gui::javascript-ready>