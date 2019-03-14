@extends('layouts.app')
@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <h1 style="text-align: center;">Your wallets:</h1>
        <input type="checkbox" v-model="hide_zero" id="hide_zero">
        <label for="hide_zero">Hide zero balances</label>
        <input type="search" class="form-control" style="width: auto;margin-bottom: 5px; float: right;"
               placeholder="Search wallets" id="search" v-model="search">
        <div class="tbl tbl-blue">
            <table class="table table-responsive-sm">
                <thead class="tbl-header">
                <tr>
                    <th>Coin</th>
                    <th>Balance</th>
                    <th>Address</th>
                    <th>Withdraw</th>
                </tr>
                </thead>
                <tbody class="tbl-content">
                <tr v-for="wallet in filteredWallets" v-show="wallet.balance>0 || !hide_zero">
                    <td>
                        <span><a v-if="wallet.name!='BTC'"
                                 v-bind:href="'/market/BTC/' +wallet.name">@{{wallet.name}}</a> <span v-else>@{{ wallet.name }}</span></span>
                    </td>
                    <td>@{{wallet.balance}}</td>
                    <td>
                        <span v-if="wallet.address!=null">@{{wallet.address}}</span>
                        <button v-else class='btn btn-outline-dark btn-create' @click="createNewAddress(wallet)">
                            Create
                        </button>
                    </td>
                    <td>
                        <a v-bind:href="'{{url('withdraw')}}/'+wallet.name"
                           class='btn btn-outline-primary'>Withdraw</a>
                    </td>
                </tr>


                </tbody>
            </table>
        </div>
    </div>
@endsection
@push ('js')
    <script src="js/wallets.js?v=2"></script>
@endpush
@push ('css')
    <link rel="stylesheet" type="text/css" href="/css/jquery.jqplot.css"/>
@endpush