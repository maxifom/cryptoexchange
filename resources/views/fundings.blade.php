@extends('layouts.app')
@section('content')
    <div class="container" style="padding-bottom: 100px;">
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div>
        @endif
        <h1 style="text-align: center;">Coin fundings:</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Coin name</th>
                        <th>Funding Address</th>
                        <th>Amount / Amount needed</th>
                        <th>Pending amount</th>
                        <th>Funded</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($fundings as $funding)
                        <tr>
                            <td><a href="{{route('coin_funding',$funding->id)}}">{{$funding->coin->name}}</a></td>
                            <td>{{$funding->address}}</td>
                            <td>{{$funding->amount}}/{{$funding->needed_amount}} ({{$funding->funding_coin->name}})</td>
                            <td>{{$funding->pending_amount}} ({{$funding->funding_coin->name}})</td>
                            <td>@if ($funding->funded===1) Funded @else Not Funded @endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
