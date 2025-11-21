<div class="input-group form-spin">
    @include('gui::forms.elements.input')
    @if($displayButtons)
    <div class="input-group-text">
        <div class="btn-group-vertical">
            <a class="btn btn-addon" data-trigger="up">
                <x-gui::tabler-icon name="caret-up" />
            </a>
            <a class="btn btn-addon" data-trigger="down">
                <x-gui::tabler-icon name="caret-down" />
            </a>
        </div>
    </div>
    @endif
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlNumber({!! $componentConfig !!});
</x-gui::javascript-ready>