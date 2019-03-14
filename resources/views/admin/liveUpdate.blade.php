@extends('admin.app')
@section('content_admin')
    <div class="container">
        <h1 style="text-align: center;">Updates:</h1>
        <h1 style="text-align: center;">Trades:</h1>
        <div class="table-scroll">
            <div class="tbl tbl-blue" style="overflow-x: hidden;">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Type</th>
                        <th>Text</th>
                        <th>Time</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($trades as $trade)
                        <tr>
                            <td>{{$trade['type']}}</td>
                            <td>{{$trade['text']}}</td>
                            <td>{{$trade['time']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <h1 style="text-align: center;">Deposits:</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue" >
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Type</th>
                        <th>Text</th>
                        <th>Time</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($deposits as $deposit)
                        <tr>
                            <td>{{$deposit['type']}}</td>
                            <td>{{$deposit['text']}}</td>
                            <td>{{$deposit['time']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <h1 style="text-align: center;">Withdrawals:</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Type</th>
                        <th>Text</th>
                        <th>Time</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($withdrawals as $withdrawal)
                        <tr>
                            <td>{{$withdrawal['type']}}</td>
                            <td>{{$withdrawal['text']}}</td>
                            <td>{{$withdrawal['time']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <h1 style="text-align: center;">Alerts:</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Type</th>
                        <th>Text</th>
                        <th>Time</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($alerts as $alert)
                        <tr>
                            <td>{{$alert['type']}}</td>
                            <td>{{$alert['text']}}</td>
                            <td>{{$alert['time']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
