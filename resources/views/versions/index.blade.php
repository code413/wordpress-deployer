@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                Versions
            </h1>
            <div class="ml-auto">
                <a href="{{action('ProfileController@index')}}" class="ml-auto">
                    <i class="fe fe-arrow-left"></i> Back
                </a>
                @if($profile->replacements()->where('type','Database')->count() > 0)

                    <a href="{{action('ReplacementController@index',['profile'=>$profile->id])}}" class="btn btn-secondary btn-sm ml-2">
                        Replacements
                    </a>
                    <a href="{{action('VersionsController@store',['profile'=>$profile->id])}}" class="btn btn-primary btn-sm ml-2">
                        Create Version
                    </a>
                @else
                    <a href="{{action('ReplacementController@create',['profile'=>$profile->id])}}" class="btn btn-primary btn-sm ml-2">
                        Create a Replacement
                    </a>
                @endif
            </div>
        </div>
        @include('partials.message')
        <div class="card">
            @if($versions->count()> 0)
                <table class="table card-table table-vcenter text-nowrap no-footer">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Active</th>
                            <th scope="col">Created at</th>
                            <th scope="col" class="text-center">Actions</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($versions as $version)
                            <tr>
                                <td scope="row">{{$version->id}}</td>
                                <td scope="row">{{$version->is_active ? 'Yes': '-'}}</td>
                                <td scope="row">
                                    <p title="{{$version->created_at}}">{{$version->created_at->diffForHumans()}}</p>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm text-success" title="Deploy"
                                                          href="{{ action('DeploymentsController',['id'=>$version->id]) }}"><i class="fe fe-upload-cloud"></i></a>
                                    <a class="btn btn-sm text-danger" title="Delete"
                                       href="{{ action('VersionsController@destroy',['id'=>$version->id]) }}">
                                        <i class="fe fe-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="card-body">
                @if($profile->replacements()->where('type','Database')->count() < 1)
                    No version available. <br>
                       No database replacement available <br>
                    Please create a database replacement to create new version. <a href="{{action('ReplacementController@create',['profile'=>$profile->id])}}">Create new replacement</a>
                @else
                    No Version available
                @endif
                    </p>
            @endif

        </div>
        <div class="pt-2">
            {{$versions->links()}}
        </div>
    </div>
@endsection
