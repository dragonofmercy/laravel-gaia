@props(['type' => \Gui\View\Components\Flash::TYPE_SUCCESS, 'dismissible' => true, 'icon' => 'circle-check', 'id' => \Ramsey\Uuid\Uuid::uuid4()])
<div id="{{ $id }}" class="toast">
    <div class="toast-body">
        <div class="d-flex gap-3 align-items-center">
            <x-gui::avatar class="text-bg-{{ $type }}-lt"><x-gui::tabler-icon name="{{ $icon }}" /></x-gui::avatar>
            <div class="flex-grow-1">
                <h4>@lang('gui::messages.generic.' . $type)</h4>
                <div>{{ $message }}</div>
            </div>
            @if($dismissible)
            <button type="button" class="btn-close align-self-start" data-bs-dismiss="toast" aria-label="Close"></button>
            @endif
        </div>
    </div>
    <x-gui::javascript-ready>
        gui.toast("#{{ $id }}");
    </x-gui::javascript-ready>
</div>