@extends('gui::layout')
@section('body-class', 'gui-layout-2cols')
@section('page-content')

    @hasSection('header')
        @yield('header')
    @else
        @include('header')
    @endif

    <div class="gui-horizontal-layout">
        <div class="sidebar-container sidebar-expand-md offcanvas offcanvas-start" id="sidebar_offcanvas" data-bs-scroll="true">
            <div class="offcanvas-header @yield('sidebar-navbar-class', 'navbar-dark') navbar-gui">
                <div class="d-flex"></div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            @yield('sidebar')

        </div>
        <main class="main-container">

            @yield('main-container')

        </main>
    </div>

    @hasSection('footer')
        @yield('footer')
    @else
        @include('footer')
    @endif

@endsection