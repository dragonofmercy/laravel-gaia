<div class="input-group input-group-flat gui-control-number">
    @if($displayButtons)
    <div class="input-group-text">
        <a class="link-secondary" data-trigger="down"><x-gui::tabler-icon name="minus" /></a>
    </div>
    @endif
    @include('gui::forms.elements.input')
    @if($displayButtons)
    <div class="input-group-text">
        <a class="link-secondary" data-trigger="up"><x-gui::tabler-icon name="plus" /></a>
    </div>
    @endif
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlNumber({!! $componentConfig !!});
</x-gui::javascript-ready>