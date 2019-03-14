<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge"> @section ('title') @show
    <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/img/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    @stack ('css')
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light" style="z-index: 2">
    <a class="navbar-brand" id="logo" href="{{route('index')}}"><img style="max-width: 90px;max-height: 32px;"
                                                                     src="/svgs/logo_small.svg"
                                                                     alt="{{env("APP_NAME")}}"></a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">


            @guest
                <li>
                <li class="nav-item @if (Route::is('login')) active @endif"><a class='nav-link'
                                                                               href="{{ route('login') }}">Login</a>
                </li>

                </li>
                <li>
                <li class="nav-item @if (Route::is('register')) active @endif"><a class='nav-link'
                                                                                  href="{{ route('register') }}">Register</a>
                </li>
                </li>
                <li class="nav-item @if (Route::is('markets')) active @endif">
                    <a class="nav-link" href="{{route('markets')}}"><i class="fas fa-exchange-alt"></i>&nbsp;Markets</a>
                </li>
                <li class="nav-item @if (Route::is('news')) active @endif">
                    <a class="nav-link" href="{{route('news')}}"><i class="fas fa-newspaper"></i>&nbsp;News</a>
                </li>
                <li class="nav-item @if (Route::is('fees')) active @endif">
                    <a class="nav-link" href="{{route('fees')}}"><i class="fas fa-percent"></i>&nbsp;Fees</a>
                </li>
        @else
            @if (Auth::user()->admin)
                <li class="nav-item  @if (Route::is('admin_dashboard')) active @endif">
                    <a class="nav-link text-danger" href="{{route('admin_dashboard')}}"><i
                                class="fas fa-tachometer-alt"></i>&nbsp;Admin Dashboard</a>
                </li>

            @endif
            <li class="nav-item @if (Route::is('wallets')) active @endif">
                <a class="nav-link" href="{{route('wallets')}}"><i class="fas fa-wallet"></i>&nbsp;Wallets</a>
            </li>
            <li class="nav-item @if (Route::is('trades')) active @endif">
                <a class="nav-link" href="{{route('trades')}}"><i class="fas fa-handshake"></i>&nbsp;Trades</a>
            </li>
            <li class="nav-item @if (Route::is('markets')) active @endif">
                <a class="nav-link" href="{{route('markets')}}"><i class="fas fa-exchange-alt"></i>&nbsp;Markets</a>
            </li>
            <li class="nav-item @if (Route::is('news')) active @endif">
                <a class="nav-link" href="{{route('news')}}"><i class="fas fa-newspaper"></i>&nbsp;News</a>
            </li>
            <li class="nav-item @if (Route::is('supportTickets')) active @endif">
                <a class="nav-link" href="{{route('supportTickets')}}"><i class="fas fa-question-circle"></i>&nbsp;Support</a>
            </li>
            <li class="nav-item @if (Route::is('api_docs')) active @endif">
                <a class="nav-link" href="{{route('api_docs')}}"><i class="fas fa-code"></i>&nbsp;API</a>
            </li>
            <li class="nav-item @if (Route::is('coin_fundings')) active @endif">
                <a class="nav-link" href="{{route('coin_fundings')}}"><i class="fas fa-coins"></i>&nbsp;Fundings</a>
            </li>
            <li class="nav-item @if (Route::is('fees')) active @endif">
                <a class="nav-link" href="{{route('fees')}}"><i class="fas fa-percent"></i>&nbsp;Fees</a>
            </li>
            @if (Auth::user()->dev===1)
                <li class="nav-item @if (Route::is('dev_requests')) active @endif">
                    <a class="nav-link" href="{{route('dev_requests')}}"><i class="fas fa-clipboard-list"></i>&nbsp;Request
                        a coin</a>
                </li>
            @endif
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>&nbsp;Account
                </a>
                <div class="dropdown-menu" id="navbar-menu" style="min-width:5rem!important"
                     aria-labelledby="navbarDropdown">
                    <a class='dropdown-item' href="{{route('depositHistory')}}">Deposit History</a>
                    <a class='dropdown-item' href="{{route('withdrawalHistory')}}">Withdrawal History</a>
                    <a class='dropdown-item' href="{{route('tradeHistory')}}">Trade History</a>
                    <a class='dropdown-item' href="{{route('settings')}}">Settings</a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>

                </div>
            </li>
            @endguest
            </ul>

    </div>
</nav>
<br>
<div id="app" style="padding-bottom: 50px;z-index: 1; display: flex;
  flex-direction: column;
  height: 100%;">
    @section('content') @show
    <notifications style="margin-top:55px;word-wrap:break-word" group="notifications"></notifications>
</div>


@include ('layouts.footer')
<script src="/js/jquery-3.3.1.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/js/modernizr-custom.js"></script>
@stack ('js')
<script src="/js/bootstrap.min.js"></script>

<script>
    if (!Modernizr.svg) {
        $("#logo img").attr("src", "/img/logo_small.png");
    }
</script>


</body>

</html>
