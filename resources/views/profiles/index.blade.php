@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                Profiles
            </h1>
            <div class="ml-auto">
                <a class="btn btn-primary btn-sm" href="{{action('ProfileController@create')}}">Create Profile</a>
            </div>
        </div>
        @include('partials.message')
        <div class="card">
            @if($profiles->count()> 0)
                <table class="table card-table table-vcenter text-nowrap no-footer">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Path from</th>
                        <th scope="col">Path to</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($profiles as $profile)
                        <tr>
                            <td scope="row">{{$profile->id}}</td>
                            <td scope="row">{{$profile->name}}</td>
                            <td scope="row">{{$profile->path_from}}</td>
                            <td scope="row">{{$profile->path_to}}</td>
                            <td class="text-center">
                                <a class="btn btn-sm" title="Version"
                                                      href="{{ action('VersionsController@index',['profile'=>$profile]) }}"><i class="fe fe-layers"></i></a>
                                <a class="btn btn-sm"
                                   href="{{ action('ProfileController@edit',['id'=>$profile->id]) }}">
                                    <i class="fe fe-edit-2"></i></a>
                                <a class="btn btn-sm text-danger" title="Delete"
                                   href="{{ action('ProfileController@destroy',['id'=>$profile->id]) }}">
                                    <i class="fe fe-trash"></i></a>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="card-body">No profile available. <a href="{{action('ProfileController@create')}}">Create a new profile</a></p>
            @endif

        </div>
        <div class="pt-2">
            {{$profiles->links()}}
        </div>
    </div>
@endsection
