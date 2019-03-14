@extends('layouts.app')
@section('content')
    <div class="container" style="height: 100%;font-family: 'Patrick Hand', cursive;">
        <div class="row justify-content-center"
             style="position: absolute;top: 0;left: 0;right: 0;bottom: 0;display: block;margin: auto;vertical-align: middle;height: 200px;">
            <div class="col-md-12" style="text-align: center;">
                <h1 class="heading">Welcome to {{env("APP_NAME")}}</h1>
                <h4 class="sub-heading">Secure digital cryptocurrency exchange</h4>
                <h3 class="text-success">Free 500 satoshi on registration</h3>
                <br>
                @guest
                    <a style="text-align: center;" class="btn btn-primary" href="{{route('register')}}">Start
                        Trading</a>
                @endguest
                @auth
                    <a style="text-align: center;" class="btn btn-primary btn-lg" href="{{route('markets')}}">Start
                        Trading</a>
                @endauth
            </div>
        </div>
    </div>

@endsection
@push ('css')
    <link href="https://fonts.googleapis.com/css?family=Patrick+Hand" rel="stylesheet">
@endpush