<div class="input-group input-group-flat form-spin">
    @include('gui::forms.elements.input')
    @if($displayButtons)
    <div class="input-group-text">
        <a data-trigger="up"></a>
        <a data-trigger="down"></a>
    </div>
    @endif
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlNumber({!! $componentConfig !!});
</x-gui::javascript-ready>