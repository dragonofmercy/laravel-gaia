<div class="modal-body text-center p-5">
    <div class="text-tertiary mb-5 w-75 mx-auto"><x-gui::illustrations.404 /></div>
    <h1>@lang("Oops… You just found an error page")</h1>
    <p class="lead">@lang("We are sorry but the page you are looking for was not found.")</p>
    <div class="row g-3 pt-3 justify-content-center">
        <div class="col-md-6 col-lg-4"><a href="{{ route('home') }}" class="btn btn-primary w-100"><x-gui::tabler-icon name="home" />@lang("Back to homepage")</a></div>
        <div class="col-md-6 col-lg-4"><x-gui::button data-bs-dismiss="modal" class="w-100">@lang("Close")</x-gui::button></div>
    </div>
</div>