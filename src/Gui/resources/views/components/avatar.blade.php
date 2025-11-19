<span {{ $attributes->merge(['class' => 'avatar', 'style' => $image]) }}>
    @if(strlen($slot))
        {{ $slot }}
    @elseif(!$image)
        <span class="initial">{{ $content }}</span>
    @endif
</span>