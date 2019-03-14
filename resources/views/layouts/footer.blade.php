@if (!Route::is('index'))
    <footer class="" style="text-align: center;flex: 0 0 auto;">
        <small style="margin: 0;"><a href="{{route('cookie_policy')}}">Cookie policy</a> | <a
                    href="{{route('privacy_policy')}}">Privacy policy</a> | <a
                    href="{{route('terms_of_service')}}">Terms of service</a></small>
        <p style="margin:0">{{env("APP_NAME")}} Copyright© 2018. All rights reserved.</p>
    </footer>
@else
    <footer class="fixed-bottom" style="text-align: center;">
        <small style="margin: 0;"><a href="{{route('cookie_policy')}}">Cookie policy</a> | <a
                    href="{{route('privacy_policy')}}">Privacy policy</a> | <a
                    href="{{route('terms_of_service')}}">Terms of service</a></small>
        <p style="margin:0">{{env("APP_NAME")}} Copyright© 2018. All rights reserved.</p>
    </footer>
@endif