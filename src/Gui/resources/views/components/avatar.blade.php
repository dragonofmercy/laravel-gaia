<div {{ $attributes->merge(['class' => 'avatar', 'style' => $image]) }}>
    @if(strlen($slot))
        {{ $slot }}
    @elseif(!$image)
        {{ $content }}
    @endif
</div>