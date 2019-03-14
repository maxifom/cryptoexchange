@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Resend confirmation token</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('resendEmail') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="email"
                                       class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="email" value="{{ $email ?? old('email') }}" required autofocus>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                    <br>
                                    @captcha
                                    <input type="text" class="form-control" style="margin-top: 5px; width:50%;"
                                           id="captcha" name="captcha">
                                    @if ($errors->has('captcha'))
                                        <span class="invalid-feedback" style="display: block;">
                                            <strong>{{ $errors->first('captcha') }}</strong>
                                            {{--<strong>Invalid captcha</strong>--}}
                                    </span>
                                    @endif
                                    <br>
                                    <button class="btn btn-primary">Resend</button>
                                    <br>
                                    <p>Or join <a href="https://discord.gg/w9BQsun" target="_blank">Discord group</a> for support
                                    </p>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
