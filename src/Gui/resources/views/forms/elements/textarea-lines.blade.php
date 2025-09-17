<div @class(['input-group gui-control-lines', 'autosize' => $autosize])>
    <div class="input-group-text">
        <div class="width-placeholder">{{ $nbLines }}</div>
        <div class="lines">
            <div class="line">1</div>
        </div>
    </div>
    @include('gui::forms.elements.textarea')
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlLines({!! $componentConfig !!})
</x-gui::javascript-ready>