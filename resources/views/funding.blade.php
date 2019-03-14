@extends('layouts.app')
@section('content')
    <div class="container" style="text-align: center">
        <h1>Funding for coin {{$funding->coin->name}}</h1>
        <h5>Progress:{{$funding->amount}}/{{$funding->needed_amount}}{{$funding->funding_coin->name}}</h5>
        @if ($funding->pending_amount!=0)<h5>Pending:{{$funding->pending_amount}}{{$funding->funding_coin->name}}</h5>@endif
        <h5>Address:{{$funding->address}} ({{$funding->funding_coin->name}})</h5>
        <h4 style="text-align: left">Deposits:</h4>
        <div class="tbl tbl-blue">
            <table class="table table-responsive-sm">
                <thead class="tbl-header">
                <th>Tx</th>
                <th>Amount</th>
                <th>Confirmations</th>
                <th>Confirmed</th>
                <th>Time</th>
                </thead>
                <tbody class="tbl-content">
                @foreach ($deposits as $deposit)

                    <tr>
                        <td>{{$deposit->tx}}</td>
                        <td>{{$deposit->value}}</td>
                        <td>{{$deposit->confirmations}} / {{$funding->needed_confirmations}}</td>
                        <td>@if ($deposit->confirmed==1) Confirmed @else Not confirmed @endif</td>
                        <td>{{$deposit->created_at}}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
