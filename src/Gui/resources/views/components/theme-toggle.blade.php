<label {{ $attributes->merge(['class' => 'swap']) }} title="{{ __($title ?? 'Nox|Lumos') }}" data-gui-behavior="theme-toggle">
    <input type="checkbox" />
    <x-gui::tabler-icon name="sun" class="swap-on" />
    <x-gui::tabler-icon name="moon" class="swap-off" />
</label>