@props(['type' => 'success', 'dismissible' => true, 'icon' => 'circle-check', 'class' => null, 'id' => \Ramsey\Uuid\Uuid::uuid4()])
<div id="{{ $id }}" class="toast">
    <div class="toast-header">
        <x-gui::tabler-icon name="{{ $icon }}" class="me-2" />
        <strong class="me-auto">System</strong>
        <small>11 mins ago</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
        {{ $message }}
    </div>
    <x-gui::javascript-ready>
        gui.toast("#{{ $id }}");
    </x-gui::javascript-ready>
</div>