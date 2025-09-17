{{ $decorator->renderHiddenFields() }}
@includeWhen($decorator->hasGlobalErrors(), 'gui::forms.decorators.default.global-errors', ['decorator' => $decorator])
@foreach($decorator->getRenderableElements()->keys() as $name)
    @include('gui::forms.decorators.default.row', ['decorator' => $decorator, 'name' => $name])
@endforeach