<form-group class="form-separator layout-grid ">
    <div class="separator-title">{{ $separator->get('title', "") }}</div>
    @if($separator->get('help'))
    <div class="separator-help">{{ $separator->get('help', "") }}</div>
    @endif
</form-group>