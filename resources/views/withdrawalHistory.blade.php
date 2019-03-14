@extends('layouts.app')
@section('content')
    <div class="container" style="text-align: center">
        <h1>Withdrawals</h1>
        <a style='float:right' class='btn btn-primary' href="{{route('withdrawalConfirmView')}}">Confirm</a>
        <div v-if="this.withdrawals.length>0" class="tbl tbl-blue">
        <table class="table table-responsive-sm">
            <thead class="tbl-header">
            <th>Name</th>
            <th>Tx</th>
            <th>Value</th>
            <th>Status</th>
            <th>Time</th>
            </thead>
            <tbody class="tbl-content">
            <tr v-for="withdrawal in withdrawals">
                <td>@{{withdrawal.name}}</td>
                <td>@{{withdrawal.tx}}
                    <small>(@{{withdrawal.address}})
                    <button v-if="withdrawal.status=='requested'" @click="cancelWithdrawal(withdrawal.id)" class="btn btn-danger btn-sm">Cancel</button>
                    </small>
                </td>
                <td>@{{withdrawal.value}}</td>
                <td>
                    <i v-if="withdrawal.status=='requested'" class="far fa-check-circle text-warning">&nbsp;@{{withdrawal.status}}</i>
                    <i v-else-if="withdrawal.status=='approved'" class="far fa-check-circle text-primary">&nbsp;@{{withdrawal.status}}</i>
                    <i v-else-if="withdrawal.status=='sent'" class="far fa-check-circle text-success">&nbsp;@{{withdrawal.status}}</i>
                </td>
                <td>@{{withdrawal.created_at}}</td>
            </tr>
            </tbody>

        </table>
        </div>
        <p v-else>You don't have any withdrawals</p>

        <button v-if="page!=0" class="btn btn-primary" @click="firstPage()"><i class="fas fa-angle-double-left"></i></button>
        <button v-if="page!=0" class="btn btn-primary" @click="prevPage()"><i class="fas fa-angle-left"></i></button>
        <button v-if="pages!=0 && pages!=-1" class="btn btn-info">@{{ page+1 }}</button>
        <button v-if="page!=pages && pages!=-1" class="btn btn-primary" @click="nextPage()"><i class="fas fa-angle-right"></i></button>
        <button v-if="page!=pages && pages!=-1" class="btn btn-primary" @click="lastPage()"><i class="fas fa-angle-double-right"></i></button>
    </div>
@endsection
@push ('js')
    <script src="js/withdrawalHistory.js"></script>
@endpush('js)