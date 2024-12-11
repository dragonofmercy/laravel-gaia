@if(\Illuminate\Support\Facades\Request::ajax())

    <div class="modal-body text-center p-5">
        <div class="fs-error text-gray-400 fw-bold">500</div>
        <p class="fs-2 fw-bold">@lang('gui::errors.500.title')</p><hr>
        <div class="fs-4 text-gray-500">@lang('gui::errors.500.clue')</div>
        <div class="gui-hidden-without-modal">
            <button type="button" class="btn btn-primary btn-lg mt-5 mx-auto col-6" data-bs-dismiss="modal">@lang('gui::errors.500.close')</button>
        </div>
    </div>

@else

    @extends('gui::layout_empty')
    @section('main-container')

        <div class="centered-container">
            <div class="card flyout rounded-3">
                <div class="card-body text-center p-5">
                    <div class="fs-error text-gray-400 fw-bold">500</div>
                    <p class="fs-2 fw-bold">@lang('gui::errors.500.title')</p><hr>
                    <div class="fs-4 text-gray-500">@lang('gui::errors.500.clue')</div>
                    <div class="d-flex flex-column flex-md-row gap-4 gap-md-5 mt-5 align-items-center justify-content-center">
                        <x-gui::link :button="true" url="{{ route('home') }}" label="gui::errors.home" class="btn-primary inline btn-lg w-50" />
                    </div>
                </div>
            </div>
        </div>

    @endsection

@endif