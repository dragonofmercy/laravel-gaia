<div class="gui-control-password input-group input-group-flat">
    @include('gui::forms.elements.input')
    <span class="input-group-text">
        @if($optionsDisplay)
        <x-gui::swap off="eye-closed" on="eye" class="link-secondary reveal" />
        @endif
        @if($optionsGenerator)
        <a class="link-secondary generator"><x-gui::tabler-icon name="adjustments" /></a>
        @endif
    </span>
</div>
@if($optionsGenerator)
<div class="gui-password-generator-template">
    <div class="gui-password-generator">
        <input class="form-control password-display" readonly />
        <div class="password-size">
            <div class="range-min"></div>
            <input class="form-range with-progress" type="range" />
            <div class="range-max"></div>
        </div>
        <div class="control">
            <x-gui::button data-trigger="randomize"><x-gui::tabler-icon name="reload" />{{ $stringGeneratePassword }}</x-gui::button>
            <div class="btn-group">
                <x-gui::button data-trigger="choose"><x-gui::tabler-icon name="circle-check" />{{ $stringChoose }}</x-gui::button>
                <x-gui::button data-trigger="choose" data-copy="1"><x-gui::tabler-icon name="copy-check" />{{ $stringChooseAndCopy }}</x-gui::button>
            </div>
        </div>
    </div>
</div>
@endif
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlPassword({!! $componentConfig !!})
</x-gui::javascript-ready>