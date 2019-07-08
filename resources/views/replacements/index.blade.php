@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                Replacements
            </h1>
            <div class="ml-auto">
                <a href="{{action('VersionsController@index',['profile'=>$profile])}}" class="ml-auto">
                    <i class="fe fe-arrow-left"></i> Back
                </a>
                <a href="{{action('ReplacementController@create',['profile'=>$profile->id])}}"
                   class="btn btn-primary btn-sm ml-2">
                    Create Replacements
                </a>
            </div>
        </div>

        @include('partials.message')
        <div class="card">
            @if($replacements->count() > 0)
                <table class="table card-table table-vcenter text-nowrap no-footer">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">From</th>
                        <th scope="col">To</th>
                        <th scope="col">Type</th>
                        <th scope="col">Path</th>
                        <th scope="col">Pattern</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($replacements as $replacement)
                        <tr>
                            <td scope="row">{{$replacement->id}}</td>
                            <td scope="row">{{$replacement->from}}</td>
                            <td scope="row">{{$replacement->to}}</td>
                            <td scope="row">{{$replacement->type}}</td>
                            <td scope="row">{{$replacement->path ?? '-'}}</td>
                            <td scope="row">{{$replacement->pattern ?? '-'}}</td>
                            <td class="text-center">
                                <a class="btn btn-sm" title="Edit" href="{{ action('ReplacementController@edit',['id'=>$replacement->id]) }}"><i class="fe fe-edit-2"></i></a>
                                <a class="btn btn-sm text-danger confirmation-alert" title="Delete"
                                   href="{{ action('ReplacementController@destroy',['replacement'=>$replacement]) }}"><i class="fe fe-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="card-body">No replacements available.
                <br>
                    Create a database replacement to create new version.<br>
                    Database replacement will create new database for deployment with new URL pointing to it.
                </p>
            @endif
        </div>
    </div>
@endsection
