@props(['type' => 'success', 'dismissible' => true, 'icon' => 'circle-check', 'class' => null])
<div @class(['alert', 'alert-' . $type, $class => $class])>
    <div class="alert-icon">
        <x-gui::tabler-icon name="{{ $icon }}" />
    </div>
    <div>{{ $message }}</div>
    @if($dismissible)
        <a class="btn-close" data-bs-dismiss="alert"></a>
    @endif
</div>