<div class="page-body">
    <div class="container-cozy mt-3 m-md-auto text-center">
        <div class="text-tertiary mb-5 w-75 mx-auto"><x-gui::illustrations.404 /></div>
        <h1>@lang("Oops… You just found an error page")</h1>
        <p class="lead">@lang("We are sorry but the page you are looking for was not found.")</p>
        <div class="d-flex flex-column flex-md-row gap-4 gap-md-5 pt-3 align-items-center justify-content-center">
            <a href="{{ route('home') }}" class="btn btn-primary"><x-gui::tabler-icon name="home" />@lang("Back to homepage")</a>
        </div>
    </div>
</div>