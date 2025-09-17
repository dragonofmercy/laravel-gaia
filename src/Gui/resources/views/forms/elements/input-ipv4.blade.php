<div {{ $attr->except('name') }}>
    <input type="text" class="form-control" size="3" maxlength="3" name="{{ $attr->get('name') }}" inputmode="numeric" autocomplete="off" value="{{ $valueMatrix[0] }}" />
    <div class="input-group-text">.</div>
    <input type="text" class="form-control" size="3" maxlength="3" name="{{ $attr->get('name') }}" inputmode="numeric" autocomplete="off" value="{{ $valueMatrix[1] }}" />
    <div class="input-group-text">.</div>
    <input type="text" class="form-control" size="3" maxlength="3" name="{{ $attr->get('name') }}" inputmode="numeric" autocomplete="off" value="{{ $valueMatrix[2] }}" />
    <div class="input-group-text">.</div>
    <input type="text" class="form-control" size="3" maxlength="3" name="{{ $attr->get('name') }}" inputmode="numeric" autocomplete="off" value="{{ $valueMatrix[3] }}" />
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlIpv4()
</x-gui::javascript-ready>