@include('gui::forms.elements.input')
@include('gui::forms.elements.input', ['attr' => $displayAttributes])
<x-gui::javascript-ready>
    $('#{{ $displayAttributes->get('id') }}').GuiControlAutocomplete({!! $componentConfig !!}).on("valueSelected.gui", (e, v, _) => {
        $("#{{ $attr->get('id') }}").val(v).trigger("change")
    }).on("input", e => {
        if($(e.currentTarget).val().length<1){
            $("#{{ $attr->get('id') }}").val("").trigger("change")
        }
    })
</x-gui::javascript-ready>