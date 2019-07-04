@extends('layouts.master')

@section('main')
    <main>
        @yield('content')
    </main>
@stop

@section('header')
    @include('partials.header')
@stop

@section('footer-scripts')
    @include('layouts.partials.footer-scripts')
@stop
