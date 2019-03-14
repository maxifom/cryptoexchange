@extends('admin.app')
@section('content_admin')
    <div class="container-fluid">
        <div class="alert alert-primary" id="message">Message</div>
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div>
        @endif
        <h1 style="text-align: center;">Coins:</h1>
        <div class="tbl tbl-blue">
            <table class="table table-responsive-sm" style="text-align: center;">
                <thead class="tbl-header">
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Check wallet</th>
                    <th>Confirm coin</th>
                    <th>Disable coin</th>
                    <th>Trading Fee</th>
                    <th>Withdrawal Fee</th>
                    <th>Change fees</th>
                </tr>
                </thead>
                <tbody class="tbl-content">
                @foreach ($coins as $coin)
                    <tr>
                        <td>{{$coin->name}}</td>
                        <td id="{{$coin->name}}-address">@if ($coin->address) {{$coin->address}}
                            @else
                                <button class='btn btn-outline-dark btn-create' name='{{$coin->name}}'>Create
                                </button>
                            @endif
                        </td>
                        <td>{{$coin->balance}}</td>
                        <td>
                            <button class="btn @if ($coin->status=='confirmed') btn-success
                                @elseif($coin->status=='created')) btn-warning
                                @elseif($coin->status=='disabled') btn-danger
                                @elseif($coin->status=='funding') btn-info
                                @endif">
                                {{strtoupper($coin->status)}}
                                @if ($coin->status=='created')
                                    <form action="{{route('admin_make_funding')}}" method="POST">
                                        Amount: <input type="text" value="0" name="needed_amount" class="form-control">
                                        BTC:<input type="radio" value="1" name="is_btc" class="form-control">
                                        {{$coin->name}}:<input type="radio" value="0" name="is_btc" class="form-control">
                                        <button class="btn btn-primary">Make funding</button>
                                        <input type="hidden" name="coin_id" value="{{$coin->id}}">
                                        @csrf
                                    </form>
                                @endif
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-outline-primary btn-check" name="{{$coin->name}}">Check wallet
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-confirm" name="{{$coin->name}}"
                                    @if ($coin->status=='confirmed') disabled @endif>Confirm
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-disable" name="{{$coin->name}}"
                                    @if ($coin->status=='disabled') disabled @endif>Disable
                            </button>
                        </td>
                        <form action="{{route('admin_change_fee')}}" method="POST">
                            <td>
                                <input type="text" value="{{$coin->trading_fee->fee}}" name="trading_fee"
                                       class="form-control">
                            </td>
                            <td>
                                <input type="text" value="{{$coin->fee->fee}}" name="fee" class="form-control">
                            </td>
                            <td>
                                <button class="btn btn-primary">Change fees</button>
                            </td>
                            @csrf
                            <input type="hidden" name="coin_id" value="{{$coin->id}}">
                        </form>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push ('js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".btn-create").click(function () {
            name = $(this).attr('name');
            $.ajax({
                method: "POST",
                url: "{{route('newAddress')}}",
                data: {name: $(this).attr('name')},
                dataType: 'json',
            })
                .done(function (msg) {
                    if (msg != -1) {
                        $("#" + name + "-address").html(msg['address']);
                    }
                    ;
                });
        });
        $(".btn-confirm").click(function () {
            name = $(this).attr('name');
            $.ajax({
                method: "POST",
                url: "{{route('saveCoin')}}",
                data: {name: $(this).attr('name'), type: 'confirmed'},
            })
                .done(function (msg) {
                    $("#message").text(msg);
                });
        });
        $(".btn-disable").click(function () {
            name = $(this).attr('name');
            $.ajax({
                method: "POST",
                url: "{{route('saveCoin')}}",
                data: {name: $(this).attr('name'), type: 'disabled'},
            })
                .done(function (msg) {
                    $("#message").text(msg);
                });
        });
        $(".btn-check").click(function () {
            name = $(this).attr('name');
            $.ajax({
                method: "POST",
                url: "{{route('checkWallet')}}",
                data: {name: $(this).attr('name')},
            })
                .done(function (msg) {
                    $("#message").text(msg);
                });
        });
    </script>
@endpush