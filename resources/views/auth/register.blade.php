@extends('layouts.auth')

@section('title', 'Automation - Register')

@section('content')
    <div class="card">
        <form class="card-body p-6" method="POST" action="{{ route('register') }}">
            <div class="card-title">{{ __('Register') }}</div>

            <div class="form-group">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input id="name" type="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       name="name" value="{{ old('name') }}" required autofocus>
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                @endif
            </div>

            <div class="form-group">
                <label for="email" class="form-label">{{ __('E-Mail Address') }}</label>
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                       email="email" value="{{ old('email') }}" required autofocus>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                @endif
            </div>

            <div class="form-group">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                       name="password" value="{{ old('password') }}" required autofocus>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                @endif
            </div>

            <div class="form-group">
                <label for="confirm-password" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="confirm-password" type="password" class="form-control{{ $errors->has('confirm_password') ? ' is-invalid' : '' }}"
                       name="password" value="{{ old('password') }}" required autofocus>
                @if ($errors->has('confirm_password'))
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('confirm_password') }}</strong>
                        </span>
                @endif
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">{{ __('Register') }}</button>
            </div>
        </form>
    </div>
@endsection

