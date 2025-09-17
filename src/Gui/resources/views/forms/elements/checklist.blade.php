<div class="form-checklist" id="{{ $attr->get('id') }}">
    <div class="checklist-header">
        <a class="link-primary" data-trigger="check">{{ $stringCheckAll }}</a>
        <div class="vr"></div>
        <a class="link-primary" data-trigger="uncheck">{{ $stringUnCheckAll }}</a>
    </div>
    <div class="checklist-content" style="--items-per-column: {{ $itemsPerColumns }}">
        @include('gui::forms.elements.input-checkbox')
    </div>
    <x-gui::javascript-ready>
        $('#{{ $attr->get('id') }}').GuiControlChecklist()
    </x-gui::javascript-ready>
</div>
