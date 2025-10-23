<div class="gui-control-password input-group input-group-flat">
    @include('gui::forms.elements.input')
    <span class="input-group-text">
        @if($optionsGenerator)
        <a class="link-secondary generator">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9.701 12.75l2.598-1.5m0 1.5l-2.598-1.5M11 10.5v3m-6.049-.75l2.598-1.5m0 1.5l-2.598-1.5m1.299-.75v3m8.25-5.75H4.75A2.25 2.25 0 0 0 2.5 10v4a2.25 2.25 0 0 0 2.25 2.25h14.5A2.25 2.25 0 0 0 21.5 14v-1.5m-1.932-6.189a1.49 1.49 0 0 1 2.106.015a1.49 1.49 0 0 1 .015 2.107l-3.809 3.809a3.5 3.5 0 0 1-1.501.888l-2.129.62l.62-2.128a3.5 3.5 0 0 1 .889-1.502z"/>
            </svg>
        </a>
        @endif
        @if($optionsDisplay)
        <x-gui::swap off="eye-closed" on="eye" class="link-secondary reveal" />
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