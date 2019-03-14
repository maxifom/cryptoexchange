@extends('layouts.app')
@section('content')
    <div class="container" style="text-align: center">
        <h1>Your deposits</h1>
        <div v-if="this.deposits.length>0" class="tbl tbl-blue">
            <table class="table table-responsive-sm">
                <thead class="tbl-header">
                <th>Name</th>
                <th>Tx</th>
                <th>Amount</th>
                <th>Confirmations</th>
                <th>Time</th>
                </thead>
                <tbody class="tbl-content">
                <tr v-for="deposit in deposits">
                    <td>@{{deposit.name}}</td>
                    <td>@{{deposit.tx}}</td>
                    <td>@{{deposit.value}}</td>
                    <td>
                        <i v-if="deposit.confirmed" class="far fa-check-circle text-success">&nbsp;Confirmed</i>
                        <span v-else>@{{deposit.confirmations}}/@{{ deposit.needed_confirmations }}</span></td>
                    <td>@{{deposit.tx_time}}</td>
                </tr>

                </tbody>
            </table>
        </div>
        <p v-else>You don't have any deposits</p>

        <button v-if="page!=0" class="btn btn-primary" @click="firstPage()"><i class="fas fa-angle-double-left"></i></button>
        <button v-if="page!=0" class="btn btn-primary" @click="prevPage()"><i class="fas fa-angle-left"></i></button>
        <button v-if="pages!=0 && pages!=-1" class="btn btn-info">@{{ page+1 }}</button>
        <button v-if="page!=pages && pages!=-1" class="btn btn-primary" @click="nextPage()"><i class="fas fa-angle-right"></i></button>
        <button v-if="page!=pages && pages!=-1" class="btn btn-primary" @click="lastPage()"><i class="fas fa-angle-double-right"></i></button>
    </div>
@endsection
@push ('js')
    <script src="js/depositHistory.js"></script>
@endpush('js)