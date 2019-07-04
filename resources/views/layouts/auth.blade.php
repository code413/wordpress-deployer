@extends('layouts.master')

@section('bodyClass') auth-layout @stop

@section('main')
   <div class="page-single">
       <div class="container">
           <div class="row">
               <div class="col col-login mx-auto">
                   @yield('content')
               </div>
           </div>
       </div>
   </div>
@stop
