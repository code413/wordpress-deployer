@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                Create a profile
            </h1>

            <a href="{{action('ProfileController@index')}}" class="ml-auto">
                <i class="fe fe-arrow-left"></i> Back
            </a>
        </div>
        <div class="card">
            @include('partials.message')
            <div class="card-body">
                <form method="POST" action="{{ action('ProfileController@store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" name="name" class="form-control" placeholder="Staging to live" required>
                    </div>
                    <div class="row">
                        <div class="col-6">   <div class="form-group">
                                <label for="db-host" class="form-label">Database Host</label>
                                <input id="db-host" type="text" name="db_host" class="form-control" placeholder="127.0.0.1" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="db-user" class="form-label">Database User</label>
                                <input id="db-user" type="text" name="db_user" class="form-control" placeholder="root" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="password" class="form-label">Database Password</label>
                                <input id="password" type="password" name="db_password" class="form-control" placeholder="password" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="db-name" class="form-label">Database Name</label>
                                <input id="db-name" type="text" name="db_name" class="form-control"  placeholder="database1" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="path-from" class="form-label">Deployment path from</label>
                                <input id="path-from" type="text" name="path_from" class="form-control"  placeholder="/home/your/project/directory/" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="path-to" class="form-label">Deployment path to</label>
                                <input id="path-to" type="text" name="path_to" class="form-control" placeholder="/home/your/project/new-directory/" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="symlink" class="form-label">Symlink path</label>
                        <input id="symlink" type="text" name="symlink" class="form-control"  placeholder="/home/your/project/domain" required>
                    </div>
                    <div class="form-group">
                        <label for="path-temp" class="form-label">Database storage path</label>
                        <input id="path-temp" type="text" name="path_temp" class="form-control" placeholder="/home/your/project/this-project/storage/app/temp/" required>
                    </div>
                    <div class="pb-3">
                        <div class="form-group">
                            <label class="custom-control custom-checkbox" for="disable-maintenance">
                                <input type="checkbox" name="disable_maintenance" class="custom-control-input" id="disable-maintenance">
                                <span class="custom-control-label">{{ __('Disable maintenance') }}</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="custom-control custom-checkbox" for="enable-gtm">
                                <input type="checkbox" name="enable_gtm" class="custom-control-input" id="enable-gtm">
                                <span class="custom-control-label">{{ __('Enable Gtm') }}</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="custom-control custom-checkbox" for="enable-indexing">
                                <input type="checkbox" name="enable_indexing" class="custom-control-input" id="enable-indexing">
                                <span class="custom-control-label">{{ __('Enable Indexing') }}</span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
