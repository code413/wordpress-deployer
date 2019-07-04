@extends('layouts.auth')

@section('title', 'Automation - Login')

@section('content')
    <div class="card">
        <form class="card-body p-6" method="POST" action="{{ route('login') }}">
            <div class="card-title">Login to your account</div>

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
                <label for="password" class="form-label">{{ __('Password') }}
                    @if (Route::has('password.request'))
                        <a class="float-right small" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </label>
                <input id="password" type="password"
                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                @endif
            </div>

            <div class="form-group">
                <label class="custom-control custom-checkbox" for="remember">
                    <input class="custom-control-input" type="checkbox" name="remember"
                           id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="custom-control-label">{{ __('Remember Me') }}</span>
                </label>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">{{ __('Login') }}</button>
            </div>
            @csrf
        </form>
    </div>
@endsection


