@extends ('layouts.app') @section ('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <h1>Wallets</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Balance</th>
                    <th>Actions</th>
                    </thead>
                    <tbody>
                    <tr v-for="wallet in wallets" v-bind:key="wallet.id" ref="wallets">
                        <td>@{{wallet.name}}</td>
                        <td v-bind:id="wallet.name+'-address'">
                            <span v-if="wallet.address!=null">@{{wallet.address}}</span>
                            <button v-else class='btn btn-outline-dark btn-create' v-bind:name=wallet.name>Create
                            </button>
                        </td>
                        <td>@{{wallet.balance}}</td>
                        <td>
                            <a v-bind:href="'{{url('withdraw')}}/'+wallet.name"
                               class='btn btn-outline-info'>Withdraw</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h1>Deposits</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                    <th>Name</th>
                    <th>Tx</th>
                    <th>Amount</th>
                    <th>Confirmations</th>
                    <th>Time</th>
                    </thead>
                    <tbody>
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
                <h1>Withdrawals</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                    <th>Name</th>
                    <th>Tx</th>
                    <th>Value</th>
                    <th>Status</th>
                    <th>Time</th>
                    </thead>
                    <tbody>
                    <tr v-for="withdrawal in withdrawals">
                        <td>@{{withdrawal.name}}</td>
                        <td>@{{withdrawal.tx}}
                            <small>(@{{withdrawal.address}})</small>
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

                <h1>Trades</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                    <th>Market</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Time</th>
                    <th>Delete</th>
                    </thead>
                    <tbody>
                    <tr v-for="trade in trades">
                        <td><a v-bind:href="'{{url('market')}}/'+trade.market">@{{trade.market}}</a></td>
                        <td>
                            <span v-if="trade.type=='sell'" class="text-success">@{{ trade.type }}</span>
                            <span v-else-if="trade.type=='buy'" class="text-danger">@{{ trade.type }}</span>
                        </td>
                        <td>@{{trade.amount}}</td>
                        <td>@{{ trade.price }}</td>
                        <td>
                            <span v-if="trade.finished==1" class="text-success">Finished</span>
                            <span v-else class="text-danger">Not finished</span>
                        </td>
                        <td>@{{ trade.updated_at }}</td>
                        <td><button class="btn btn-outline-danger" @click="deleteTrade(trade.id)">Delete</button></td>
                    </tr>
                    </tbody>
                </table>


            </div>
        </div>
    </div>
@endsection @push ('js')
    @auth
        <script src="{{asset('js/balances.js')}}"></script>@endauth
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
                data: {name: $(this).attr('name')}
            })
                .done(function (msg) {
                    if (msg != -1) {
                        $("#" + name + "-address").html(msg);
                    }
                    ;
                });
        });
    </script>
@endpush