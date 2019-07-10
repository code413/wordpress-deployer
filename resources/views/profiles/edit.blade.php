@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                Edit Profile
            </h1>
            <a href="{{action('ProfileController@index')}}" class="ml-auto">
                <i class="fe fe-arrow-left"></i> Back
            </a>
        </div>

        <div class="card">
            @include('partials.message')
            <div class="card-body">
                <form method="POST" action="{{ action('ProfileController@update',['profile'=>$profile]) }}">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" name="name" class="form-control" value="{{ $profile->name }}"
                               placeholder="Live to staging" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="db-host" class="form-label">Database Host</label>
                                <input id="db-host" type="text" name="db_host" value="{{ $profile->db_host }}" class="form-control"
                                       placeholder="127.0.0.1" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="db-user" class="form-label">Database User</label>
                                <input id="db-user" type="text" name="db_user" value="{{ $profile->db_user }}" class="form-control"
                                       placeholder="root" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="db-password" class="form-label">Database Password</label>
                                <input id="db-password" type="password" name="db_password" value="{{ $profile->password }}"
                                       class="form-control" placeholder="password" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="path-temp" class="form-label">Database storage path</label>
                                <input id="path-temp" type="text" name="path_temp" class="form-control" value="{{ $profile->path_temp }}"
                                       placeholder="/home/your/project/this-project/storage/app/temp/" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="path-from" class="form-label">Deployment path from</label>
                                <input id="path-from" type="text" name="path_from" value="{{ $profile->path_from }}"
                                       class="form-control" placeholder="/home/your/project/directory/" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="path-to" class="form-label">Deployment path to</label>
                                <input id="path-to" type="text" name="path_to" value="{{ $profile->path_to }}" class="form-control"
                                       placeholder="/home/your/project/new-directory/" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="symlink" class="form-label">Symlink path</label>
                        <input id="symlink" type="text" name="symlink" class="form-control" value="{{ $profile->symlink }}"
                               placeholder="/home/your/project/domain" required>
                    </div>

{{--                    <div class="pb-3">--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="custom-control custom-checkbox" for="enable-indexing">--}}
{{--                                    <input--}}
{{--                                            @if(isset(json_decode($profile->options)->enable_indexing) && json_decode($profile->options)->enable_indexing == 'on') checked--}}
{{--                                            @endif  type="checkbox" name="enable_indexing" class="custom-control-input"--}}
{{--                                            id="enable-indexing">--}}
{{--                                    <span class="custom-control-label">{{ __('Enable Search Engine Indexing') }}</span>--}}
{{--                                </label>--}}
{{--                            </div>--}}

{{--                            <label class="custom-control custom-checkbox" for="disable-maintenance">--}}
{{--                                <input--}}
{{--                                    @if(isset(json_decode($profile->options)->disable_maintenance) && json_decode($profile->options)->disable_maintenance == 'on') checked--}}
{{--                                    @endif--}}
{{--                                    type="checkbox" name="disable_maintenance" class="custom-control-input"--}}
{{--                                    id="disable-maintenance">--}}
{{--                                <span class="custom-control-label">{{ __('Disable maintenance') }}</span>--}}
{{--                                <small>*Only compatible with Maintenance plugin</small>--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}

{{--                            <label class="custom-control custom-checkbox" for="enable-gtm">--}}
{{--                                <input--}}
{{--                                    @if(isset(json_decode($profile->options)->enable_gtm) && json_decode($profile->options)->enable_gtm == 'on') checked--}}
{{--                                    @endif type="checkbox" name="enable_gtm" class="custom-control-input"--}}
{{--                                    id="enable-gtm">--}}
{{--                                <span class="custom-control-label">{{ __('Enable Gtm') }}</span>--}}
{{--                                <small>*Only compatible with Google Tag Manager for Wordpress plugin</small>--}}
{{--                            </label>--}}
{{--                        </div>--}}

{{--                    </div>--}}

                    <div class="pb-3">

                        <div class="form-group">
                            <div class="form-label">Search Engine Indexing</div>
                            <div class="custom-controls-stacked">
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="enable_indexing" value="yes"
                                           @if(json_decode($profile->options)->enable_indexing == 'yes') checked @endif>
                                    <span class="custom-control-label">Enable</span>
                                </label>
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="enable_indexing" value="no"
                                           @if(json_decode($profile->options)->enable_indexing == 'no') checked @endif>
                                    <span class="custom-control-label">Disable</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label">Maintenance mode</div>
                            <small>*Only compatible with Maintenance plugin</small>
                            <div class="custom-controls-stacked">
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="disable_maintenance" value="yes"
                                           @if(json_decode($profile->options)->disable_maintenance == 'yes') checked @endif>
                                    <span class="custom-control-label">Enable</span>
                                </label>
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="disable_maintenance" value="no"
                                           @if(json_decode($profile->options)->disable_maintenance == 'no') checked @endif>
                                    <span class="custom-control-label">Disable</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-label">GTM</div>
                            <small>*Only compatible with Google Tag Manager for Wordpress plugin</small>
                            <div class="custom-controls-stacked">
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="enable_gtm" value="yes"
                                           @if(json_decode($profile->options)->enable_gtm == 'yes') checked @endif>
                                    <span class="custom-control-label">Enable</span>
                                </label>
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="enable_gtm" value="no"
                                           @if(json_decode($profile->options)->enable_gtm == 'no') checked @endif>
                                    <span class="custom-control-label">Disable</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
