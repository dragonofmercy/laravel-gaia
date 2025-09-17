@include('gui::forms.elements.input-group')
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiCalendar({!! $componentConfig !!})
</x-gui::javascript-ready>