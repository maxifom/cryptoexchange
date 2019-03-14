@extends('layouts.app')

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status')) <div class="alert alert-primary">{{session('status')}}</div> @endif
                <div class="card">
                    <div class="card-header">{{ __('Confirm Email') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('confirmEmail') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="token"
                                       class="col-sm-4 col-form-label text-md-right">{{ __('Token') }}</label>

                                <div class="col-md-6">
                                    <input id="token" type="text"
                                           class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}"
                                           name="token" required autofocus>

                                    @if ($errors->has('token'))
                                        <span class="invalid-feedback">
                                        <strong>{{ $errors->first('token') }}</strong>
                                    </span>
                                    @endif
                                    <p style="margin-top: 10px;">Did not receive confirmation email? <a href="{{route('resendEmail_form')}}">Resend email</a></p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Confirm') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
