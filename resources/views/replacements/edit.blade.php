@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                Edit Replacement
            </h1>
            <a href="{{action('ReplacementController@index',['profile'=>$replacement->profile_id])}}" class="ml-auto">
                <i class="fe fe-arrow-left"></i> Back
            </a>
        </div>

        <div class="card">
                <div class="card-body">
                    @include('partials.message')
                    <form method="POST" action="{{ action('ReplacementController@update',['replacement'=>$replacement->id]) }}">
                        @csrf
                        @method('put')
                        <div class="form-group">
                            <label for="from" class="form-label">Replace From</label>
                            <input id="from" type="text" name="from" class="form-control"  value="{{$replacement->from}}" placeholder="Current URL" required>
                        </div>
                        <div class="form-group" class="form-label">
                            <label for="to">Replace To</label>
                            <input id="to" type="text" name="to" class="form-control"  value="{{$replacement->from}}" placeholder="New URL" required>
                        </div>
                        <div class="form-group">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" class="form-control" id="type">
                                <option @if($replacement->type === 'Database') selected @endif>Database</option>
                                <option @if($replacement->type === 'File') selected @endif>File</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="path" class="form-label">Path</label>
                            <input id="path" type="text" name="path" class="form-control" value="{{$replacement->path}}" placeholder="path">
                        </div>
                        <div class="form-group">
                            <label for="pattern" class="form-label">Pattern</label>
                            <input id="pattern" type="text" name="pattern" class="form-control" value="{{$replacement->pattern}}" placeholder="*.php">
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
    </div>
@endsection
