@extends('layouts.auth')

@section('title', 'Automation - Reset Password')

@section('content')
    <div class="card">
        <form class="card-body p-6" method="POST" action="{{ route('password.update') }}">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card-title">{{ __('Reset Password') }}</div>

            <div class="form-group">
                <label for="email" class="form-label">{{ __('E-Mail Address') }}</label>
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                       name="email" value="{{ old('email') }}" required autofocus>
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
                <button type="submit" class="btn btn-primary btn-block">{{ __('Reset Password') }}</button>
            </div>

            <input type="hidden" name="token" value="{{ $token }}">
            @csrf
        </form>
    </div>
@endsection
