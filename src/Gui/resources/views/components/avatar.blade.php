<div {{ $attributes->merge(['class' => 'avatar', 'style' => $renderAvatarImage()]) }}>
    @if(strlen($slot))
        {{ $slot }}
    @elseif(!$renderAvatarImage())
        {{ $content }}
    @endif
</div>