@extends('layouts.app')
@section('content')
    <div class="container" style="text-align: center">
        <h1>Your trade history</h1>
        <div v-if="this.trades.length>0" class="tbl tbl-blue">
            <table class="table table-responsive-sm">
                <thead class="tbl-header">
                <th>Market</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Price</th>
                <th>Total</th>
                <th>Time</th>
                </thead>
                <tbody class="tbl-content">
                <tr v-for="trade in trades">
                    <td><a v-bind:href="'/market/'+trade.market">@{{ trade.market }}</a></td>
                    <td>@{{ trade.type }}</td>
                    <td>@{{ trade.amount }}</td>
                    <td>@{{ trade.price }}</td>
                    <td>@{{ (trade.price * trade.amount).toFixed(8) }}</td>
                    <td>@{{ trade.created_at }}</td>
                </tr>

                </tbody>
            </table>
        </div>
        <p v-else>You don't have any finished trades</p>
        <button v-if="page!=0" class="btn btn-primary" @click="firstPage()"><i class="fas fa-angle-double-left"></i></button>
        <button v-if="page!=0" class="btn btn-primary" @click="prevPage()"><i class="fas fa-angle-left"></i></button>
        <button v-if="pages!=0 && pages!=-1" class="btn btn-info">@{{ page+1 }}</button>
        <button v-if="page!=pages && pages!=-1" class="btn btn-primary" @click="nextPage()"><i class="fas fa-angle-right"></i></button>
        <button v-if="page!=pages && pages!=-1" class="btn btn-primary" @click="lastPage()"><i class="fas fa-angle-double-right"></i></button>
    </div>
@endsection
@push ('js')
    <script src="js/tradeHistory.js"></script>
@endpush('js)