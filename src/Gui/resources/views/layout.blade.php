@if(\Illuminate\Support\Facades\Request::ajax())

@yield('main-content')

@else

<!DOCTYPE html>
@if(config('gui.force_darkmode'))
    <html data-theme="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif
<head>
    <title>@yield('title', 'Laravel')</title>
    @stack('metas')
    @stack('links')
    @stack('scripts')
</head>
<body class="@yield('body-class')">
    <div class="modal fade" id="gui_modal">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>
    @yield('page-content')
    @stack('deferred-script')
</body>
</html>

@endif