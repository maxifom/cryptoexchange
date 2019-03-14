@extends('layouts.app')
@section('content')
    <div class="container" style="text-align: center; padding-bottom: 50px;">
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div> @endif
        <h1>Open trades:</h1>
        <div v-if="this.trades.length>0" class="tbl tbl-blue">
            <table class="table table-responsive-sm">
                <thead class="tbl-header">
                <tr>
                    <th>Market</th>
                    <th>Type</th>
                    <th>Amount traded/Amount</th>
                    <th>Price</th>
                    <th>Time</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody class="tbl-content">
                <tr v-for="trade in trades">
                    <td><a v-bind:href="'/market/'+trade.market">@{{trade.market}}</a></td>
                    <td>@{{trade.type}}</td>
                    <td>@{{ trade.amount_traded}}/@{{trade.amount}}</td>
                    <td>@{{ trade.price }}</td>
                    <td>@{{ trade.updated_at }}</td>
                    <td>
                        <button @click='deleteTrade(trade.id)' class="btn btn-outline-danger">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <p v-else>You don't have any open trades</p>
        <button v-if="page!=0" class="btn btn-primary" @click="firstPage()"><i class="fas fa-angle-double-left"></i>
        </button>
        <button v-if="page!=0" class="btn btn-primary" @click="prevPage()"><i class="fas fa-angle-left"></i></button>
        <button v-if="pages!=0 && pages!=-1" class="btn btn-info">@{{ page+1 }}</button>
        <button v-if="page!=pages && pages!=-1" class="btn btn-primary" @click="nextPage()"><i
                    class="fas fa-angle-right"></i></button>
        <button v-if="page!=pages && pages!=-1" class="btn btn-primary" @click="lastPage()"><i
                    class="fas fa-angle-double-right"></i></button>
        <br>
            <br>

            <a class='btn btn-primary' href="{{route('tradeHistory')}}">Trade History</a>
    </div>
@endsection
@push('js')
    <script src="{{asset('js/trades.js')}}"></script>
@endpush