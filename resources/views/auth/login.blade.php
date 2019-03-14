@extends('layouts.app')

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-primary">{{session('status')}}</div> @endif
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="email"
                                       class="col-sm-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" autocomplete="email"
                                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="email" value="{{ old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" autocomplete="password"
                                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="one_time_password"
                                       class="col-md-4 col-form-label text-md-right">{{ __('One time password') }}</label>

                                <div class="col-md-6">
                                    <input id="one_time_password" type="number" autocomplete="off"
                                           class="form-control{{ $errors->has('one_time_password') ? ' is-invalid' : '' }}"
                                           name="one_time_password">

                                    @if ($errors->has('one_time_password'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('one_time_password') }}</strong>
                                    </span>
                                    @endif
                                    <small class="form-text text-muted">Leave blank if you didn't set up a 2fa
                                        authentication
                                    </small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"
                                                   name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                    @captcha
                                    <input type="text" class="form-control" style="margin-top: 5px; width:50%;"
                                           id="captcha" name="captcha" autocomplete="off">
                                    @if ($errors->has('captcha'))
                                        <span class="invalid-feedback" style="display: block;">
                                            <strong>{{ $errors->first('captcha') }}</strong>
                                            {{--<strong>Invalid captcha</strong>--}}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                    <p style="margin-top: 10px;">Not registered? <a href="{{route('register')}}">Sign
                                            up</a></p>
                                    <p style="margin-top: 10px;">Did not receive confirmation email? <a href="{{route('resendEmail_form')}}">Resend email</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
