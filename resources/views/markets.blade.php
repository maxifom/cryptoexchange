@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 style="text-align: center;">Markets:</h1>
        <input type="search" class="form-control" style="width: auto;margin-bottom: 5px; float: right;" placeholder="Search markets" id="search" v-model="search">
        <div class="tbl tbl-blue">
            <table class="table table-responsive-sm" style="text-align: center;">
                <thead class="tbl-header">
                <tr>
                    <th>Name</th>
                    <th>Volume</th>
                    <th>Trade count</th>
                </tr>
                </thead>
                <tbody class="tbl-content">
                <tr v-bind:id="market.id" v-for="market in filteredMarkets">
                    <td><a v-bind:href="'/market/'+market.name">@{{market.name}}</a></td>
                    <td>@{{market.volume_base.toFixed(8)}} @{{ market.base_name }}</td>
                    <td>@{{ market.trade_count_24hrs }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{asset('js/markets.js')}}?v=3"></script>
@endpush