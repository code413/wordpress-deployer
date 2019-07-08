@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                Create a replacement
            </h1>

            <a href="{{action('ReplacementController@index',['profile'=>$profile->id])}}" class="ml-auto">
                <i class="fe fe-arrow-left"></i> Back
            </a>
        </div>

        <div class="card">
            <form class="card-body" method="POST"
                  action="{{ action('ReplacementController@store',compact('profile')) }}">
                @include('partials.message')
                @csrf
                <div class="form-group">
                    <label for="from" class="form-label">Replace From</label>
                    <input id="from" type="text" name="from" class="form-control" aria-describedby="emailHelp"
                           placeholder="Current URL" required value="{{ old('from') }}">
                </div>
                <div class="form-group">
                    <label for="to" class="form-label">Replace To</label>
                    <input id="to" type="text" name="to" class="form-control" aria-describedby="emailHelp"
                           placeholder="New URL" required value="{{ old('to') }}">
                </div>
                <div class="form-group">
                    <label for="type" class="form-label">Type</label>
                    <select id="type" name="type" class="form-control replacement-type">
                        <option {{ old('type') === 'Database' ? 'selected' : ''}}>Database</option>
                        <option {{ old('type') === 'File' ? 'selected' : ''}}>File</option>
                    </select>
                </div>
                <div class="form-group file-selection" {{ old('type') && old('type') === 'File' ? '' : 'hidden'}}>
                    <label for="path" class="form-label">Path </label>
                    <input id="path" type="text" name="path" class="form-control" placeholder="folder-name" value="{{ old('path') }}">
                    <small>* Required if file type selected.</small>
                </div>
                <div class="form-group file-selection" {{ old('type') && old('type') === 'File' ? '' : 'hidden'}}>
                    <label for="pattern" class="form-label">Pattern</label>
                    <input id="pattern" type="text" name="pattern" class="form-control" placeholder="*.php" value="{{ old('pattern') }}">
                    <small>* Required if specific files only.</small>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
