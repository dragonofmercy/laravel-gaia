<form-group class="layout-grid form-separator">
    <div class="separator-title">{{ $separator->get('title', "") }}</div>
    @if($separator->get('help'))
    <div class="separator-help">{{ $separator->get('help', "") }}</div>
    @endif
</form-group>