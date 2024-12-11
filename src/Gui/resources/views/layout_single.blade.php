@extends('gui::layout')

@section('page-content')

    @hasSection('header')
        @yield('header')
    @else
        @include('header')
    @endif

    <main class="main-container">
        @yield('main-container')
    </main>

    @hasSection('footer')
        @yield('footer')
    @else
        @include('footer')
    @endif

@endsection