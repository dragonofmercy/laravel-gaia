<div class="gui-control-image">
    @include('gui::forms.elements.input')
    <div class="thumbnail" style="{{ $sizeStyle }}">
        <div class="control">
            <x-gui::button class="btn-square btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('gui::messages.component.image.upload')" data-trigger="browse"><x-gui::tabler-icon name="upload" /></x-gui::button>
            <x-gui::button class="btn-square btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('gui::messages.component.image.trash')" data-trigger="clear"><x-gui::tabler-icon name="eraser" /></x-gui::button>
        </div>
    </div>
    <div class="size">{{ $sizeDisplay }}</div>
    <x-gui::layouts.modal id="{{ $attr->get('id') }}_modal" modal-size="modal-xl">
        <div class="modal-body cropper-modal-loading">
            <div class="cropper-crop-container">
                <img class="gui-cropper-image" src />
            </div>
        </div>
        <div class="modal-footer justify-content-between gap-3">
            <x-gui::button class="btn-icon-lg" data-bs-dismiss="modal"><x-gui::tabler-icon name="x" />@lang('gui::messages.component.image.close')</x-gui::button>
            <div class="d-flex gap-3">
                <x-gui::button data-trigger="reset" class="btn-icon-lg" ><x-gui::tabler-icon name="history" />@lang('gui::messages.component.image.reset')</x-gui::button>
                <x-gui::button data-trigger="crop" class="btn-icon-lg" data-loading-text="{{ trans('gui::messages.generic.loading') }}"><x-gui::tabler-icon name="crop" />@lang('gui::messages.component.image.crop')</x-gui::button>
            </div>
        </div>
    </x-gui::layouts.modal>
</div>
<x-gui::javascript-ready>
    $('#{{ $attr->get('id') }}').GuiControlImage({!! $componentConfig !!})
</x-gui::javascript-ready>