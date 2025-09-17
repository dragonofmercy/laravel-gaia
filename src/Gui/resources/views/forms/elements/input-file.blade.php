<div class="gui-control-file">
    @include('gui::forms.elements.input')
    @include('gui::forms.elements.input-group', ['attr' => $inputAttributes])
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlFile({
        stringSelectedFiles: "{{ $stringSelectedFiles }}"
    })
</x-gui::javascript-ready>