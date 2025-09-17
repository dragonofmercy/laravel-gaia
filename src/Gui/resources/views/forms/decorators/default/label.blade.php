<div class="control-label">
@if($decorator->hasLabel($name))
    <label for="{{ $decorator->generateId($name) }}" @if($decorator->isRequired($name)) class="required" @endif>{{ $decorator->generateLabel($name) }}</label>
@endif
</div>