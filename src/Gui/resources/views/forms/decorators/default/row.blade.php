@includeWhen($decorator->hasSeparator($name), 'gui::forms.decorators.separator', ['separator' => $decorator->getSeparator($name)])
<form-group @class(['has-error' => $decorator->hasError($name)])>
    @include('gui::forms.decorators.default.label', ['decorator' => $decorator, 'name' => $name])
    @include('gui::forms.decorators.default.element', ['decorator' => $decorator, 'name' => $name])
</form-group>