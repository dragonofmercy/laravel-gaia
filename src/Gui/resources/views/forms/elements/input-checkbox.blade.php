<div @class([$elementClass, 'inline' => $inline])>
    @foreach($choices as $value => $name)
    <div class="{{ $groupClass }}">
        @include('gui::forms.elements.input', ['attr' => $choicesAttributes->get($value)])
        <label class="{{ $labelClass }}" for="{{ $choicesAttributes->get($value)->get('id') }}">{{ $name }}</label>
    </div>
    @endforeach
</div>