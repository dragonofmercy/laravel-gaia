<label {{ $attributes->merge(['class' => 'swap'])->except(['on', 'off']) }}>
    <input type="checkbox" />
    <div class="swap-on"><x-gui::tabler-icon name="{{ $on }}" /></div>
    <div class="swap-off"><x-gui::tabler-icon name="{{ $off }}" /></div>
</label>