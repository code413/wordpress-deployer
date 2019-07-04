@extends('layouts.auth')

@section('content')
    <div class="card">
        <form class="card-body p-6" method="POST" action="{{ route('password.email') }}">

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

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">{{ __('Send Password Reset Link') }}</button>
            </div>

            @csrf
        </form>
    </div>
@endsection
