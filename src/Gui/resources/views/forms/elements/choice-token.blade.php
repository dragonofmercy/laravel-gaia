@include('gui::forms.elements.choice-select')
<div {{ $displayAttributes }}></div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlToken({!! $componentConfig !!});
</x-gui::javascript-ready>