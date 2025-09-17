<div class="control-field">
    <div data-size="{{ $decorator->getElementSize($name) }}">
        {{ $decorator->renderElement($name) }}
    </div>
    @includeWhen($decorator->hasError($name), 'gui::forms.decorators.default.invalid-feedback', ['decorator' => $decorator, 'name' => $name])
    @includeWhen($decorator->hasHint($name), 'gui::forms.decorators.default.field-hint', ['decorator' => $decorator, 'name' => $name])
</div>