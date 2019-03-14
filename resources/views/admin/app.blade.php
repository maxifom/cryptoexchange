@extends ('layouts.app')
@section ('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div>
        @endif
        <div class="row justify-content-center" style="text-align: center;">
            <div style="margin-right: 5px;">
                <a href="/admin/addCoin" class="btn btn-outline-primary">Add coin</a>
            </div>
            <div style="margin-right: 5px;">
                <a href="/admin/coins" class="btn btn-outline-primary">Coins</a>
            </div>
            <div style="margin-right: 5px;">
                <a href="/admin/liveUpdates" class="btn btn-outline-primary">Live updates</a>
            </div>
            <div style="margin-right: 5px;">
                <a href="/admin/serverStats" class="btn btn-outline-primary">Server stats</a>
            </div>
            <div style="margin-right: 5px;">
                <a href="/admin/supportTickets" class="btn btn-outline-primary">Support tickets</a>
            </div>
        </div>
        <div class="row justify-content-center" style="text-align: center;margin-top: 10px;">
            <div style="margin-right: 5px;">
                <a href="/admin/addNews" class="btn btn-outline-primary">Add News</a>
            </div>
            <div style="margin-right: 5px;">
                <a href="/admin/news" class="btn btn-outline-primary">News</a>
            </div>

            <div style="margin-right: 5px;">
                <a href="/admin/coinInfos" class="btn btn-outline-primary">CoinInfos</a>
            </div>
            <div>
                <a href="/admin/requests" class="btn btn-outline-primary">Coin Requests</a>
            </div>
        </div>
        <div class="row justify-content-center" style="text-align: center;margin-top: 10px;">
            <div style="margin-right: 5px;">
                <a href="/admin/users" class="btn btn-outline-primary">Users</a>
            </div>
        </div>
        <div class="row justify-content-center" style="text-align: center;margin-top: 20px;margin-bottom: 20px;">
            <div style="margin-right: 5px;">
                <a href="/admin/laraadmin" class="btn btn-outline-primary">LaraAdmin</a>
            </div>
            <div style="margin-right: 5px;">
                <a href="/admin/lapse" class="btn btn-outline-primary">Lapse</a>
            </div>
            <div style="margin-right: 5px;">
                <a href="/admin/horizon" class="btn btn-outline-primary">Horizon</a>
            </div>

        </div>
    </div>
    @yield('content_admin')
@endsection