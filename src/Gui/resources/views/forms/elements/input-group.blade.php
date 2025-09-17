@props(['flat' => true])
<div @class(['input-group', 'input-group-flat' => $flat])>
    @isset($prefix)
        @if(!$flat) {{ $prefix }} @else <div class="input-group-text">{{ $prefix }}</div> @endif
    @endif
    @include('gui::forms.elements.input')
    @isset($suffix)
        @if(!$flat) {{ $suffix }} @else <div class="input-group-text">{{ $suffix }}</div> @endif
    @endif
</div>