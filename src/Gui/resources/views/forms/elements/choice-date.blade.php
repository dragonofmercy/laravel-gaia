<div {{ $attr }}>
@foreach($selectors as $item => $choices)
    @include('gui::forms.elements.choice-select', ['attr' => $selectorsAttributes->get($item), 'options' => $choices, 'optionsAttributes' => $optionsAttributes->get($item)])
@endforeach
</div>