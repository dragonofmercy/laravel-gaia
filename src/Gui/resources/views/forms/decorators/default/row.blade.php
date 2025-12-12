@if(($decorator->hasSeparator($name)))
    @include('gui::forms.decorators.default.separator', ['separator' => $decorator->getSeparator($name)])
@endif
<form-group @class(['has-error' => $decorator->hasError($name)])>
    @include('gui::forms.decorators.default.label', ['decorator' => $decorator, 'name' => $name])
    @include('gui::forms.decorators.default.element', ['decorator' => $decorator, 'name' => $name])
</form-group>