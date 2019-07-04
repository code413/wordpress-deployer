<!DOCTYPE html>
<html lang='en' class="@yield('superclass')">
    <head>
        @section('head')
            <title>@yield('title', 'Wordpress Deployer')</title>
            @include('layouts.partials.meta')
            @include('layouts.partials.favicon')
            @include('layouts.partials.styles')
            @include('layouts.partials.head-scripts')

            @if(config('app.env') == 'production')
                @include('layouts.partials.trackers')
            @endif
        @show

        @section('meta-og-image')

        @show
    </head>
    <body class="page @yield('bodyClass')" id="app">
        <div class="flex-fill">
            @section('header')

            @show

            @section('main')

            @show

            @section('footer')

            @show
        </div>

        @section('footer-scripts')

        @show
    </body>
</html>
