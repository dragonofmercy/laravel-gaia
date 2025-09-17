@includeWhen($decorator->hasSeparator($name), 'gui::forms.decorators.grid.separator', ['separator' => $decorator->getSeparator($name)])
<form-group @class(['layout-grid', 'has-error' => $decorator->hasError($name)])>
    @include('gui::forms.decorators.default.label', ['decorator' => $decorator, 'name' => $name])
    @include('gui::forms.decorators.default.element', ['decorator' => $decorator, 'name' => $name])
</form-group>