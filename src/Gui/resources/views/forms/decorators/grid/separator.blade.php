<form-group class="form-separator layout-grid">
    @if($separator->get('icon')) <div class="icon-sm">{{ $separator->get('icon') }}</div> @endif
    <div class="d-flex flex-column">
        <div class="separator-title">{{ $separator->get('title', "") }}</div>
        @if($separator->get('help'))
        <div class="separator-help">{{ $separator->get('help', "") }}</div>
        @endif
    </div>
</form-group>