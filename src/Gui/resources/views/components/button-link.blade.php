<x-gui::link {{ $attributes->merge(['class' => 'btn']) }}>
    @if($attributes->has('data-loading-text'))
        <div data-loading-content>
            <div class="loader loader-{{ $loadingStyle }}"></div>
            {{ $attributes->get('data-loading-text', '') }}
        </div>
    @endif
    {{ $slot }}
</x-gui::link>