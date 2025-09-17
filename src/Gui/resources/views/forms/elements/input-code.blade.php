<div {{ $attr }}>
@foreach($pieces as $piece)
    {{ $piece }}
@endforeach
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlCode({
        pattern: {{ $componentConfigPattern }}
    })
</x-gui::javascript-ready>