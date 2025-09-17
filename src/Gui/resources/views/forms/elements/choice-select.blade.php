<select {{ $attr }}>
@foreach($options as $value => $name)
    @if(is_iterable($name))
        <optgroup label="{{ $value }}">
        @foreach($name as $groupValue => $groupValueName)
            <option {{ $optionsAttributes->get($groupValue) }}>{{ $groupValueName }}</option>
        @endforeach
        </optgroup>
    @else
        <option {{ $optionsAttributes->get($value) }}>{{ $name }}</option>
    @endif
@endforeach
</select>