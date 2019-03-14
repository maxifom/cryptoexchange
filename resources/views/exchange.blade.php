@extends ('layouts.app') @section ('content')

    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="d-none d-lg-block col-lg-2 table-scroll-left">
                <div class="tbl tbl-gray">
                    <table class="table table-responsive-sm" style="text-align: center;">
                        <thead>
                        <tr>
                            <th>
                                <small>Name</small>
                            </th>
                            <th>
                                <small>Volume</small>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="_market in markets">
                            <td>
                                <small><a v-bind:href="'/market/'+_market.name">@{{_market.trade_name}}</a></small>
                            </td>
                            <td>
                                <small>@{{printNormal(_market.volume_base)}}</small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>


            </div>
            <div class="col-lg-10">
                <h1 style='text-align:center'>{{$base_coin}}/{{$trade_coin}}</h1>

                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <table class="table table-bordered table-sm table-responsive-sm">
                            <thead>
                            <h6 style="text-align:center">Last 24 hours stats</h6>
                            <th>Low</th>
                            <th>High</th>
                            <th>Open</th>
                            <th>Close</th>
                            <th>Trades</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{$market->low}}</td>
                                <td>{{$market->high}}</td>
                                <td>{{$market->open}}</td>
                                <td>{{$market->close}}</td>
                                <td>{{$market->trade_count_24hrs}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div style="margin-left:7.5px;margin-right:7.5px;">
                    <div id="chart_plotly"></div>
                </div>
                <div v-if="status" class="alert alert-primary">
                    @{{ status }}
                </div>
                <div class="row">

                    <div class="col-sm">
                        <div>
                            <h2>Buy {{$trade_coin}}</h2>
                            <p v-if="wallet_base!=null">Balance: @{{ wallet_base.balance }} @{{ wallet_base.name }}</p>
                            <form enctype="multipart/form-data" method="POST" @submit.prevent="buy()" ref="buy_form">
                                <div class="form-group">
                                    <label for="buy_amount">Amount</label>
                                    <div class="input-group">
                                        <input id="buy_amount" class="form-control" type="text" name="amount"
                                               v-bind:value="printNormal(buy_trade.amount)" v-on:change="buy_amount()"
                                               ref="buy_amount" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$trade_coin}}</span>
                                        </div>
                                    </div>
                                    <label for="buy_price">Price</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="buy_price" name="price"
                                               v-bind:value="printNormal(buy_trade.price)" v-on:change="buy_price()"
                                               ref="buy_price" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$base_coin}}</span>
                                        </div>
                                    </div>
                                    <label for="buy_sum">Sum</label>
                                    <div class="input-group">
                                        <input id="buy_sum" class="form-control" type="text"
                                               v-bind:value="printNormal(buy_trade.amount_base)" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$base_coin}}</span>
                                        </div>
                                    </div>
                                    <label for="buy_fee">Fee (@{{ fee*100 }}% min:1sat )</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="buy_fee"
                                               v-bind:value="printNormal(buy_trade.amount_base*fee)" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$base_coin}}</span>
                                        </div>
                                    </div>
                                    <label for="buy_price">Total</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="buy_total"
                                               v-bind:value="printNormal(buy_trade.amount_base*(plus))"
                                               v-on:change="buy_total()" ref="buy_total">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$base_coin}}</span>
                                        </div>
                                    </div>
                                    <br>
                                    <button class="btn btn-outline-success">Buy {{$trade_coin}}</button>
                                </div>
                                <input type="hidden" name="market_id" v-bind:value="market_id">
                                <input type="hidden" name="type" value="buy">
                                {{csrf_field()}}
                            </form>
                        </div>
                        <h2 style='text-align:center'>Sell orders</h2>
                        <div class="table-scroll">
                            <div class="tbl tbl-green">
                                <table class="table table-hover table-bordered sell_table"
                                       style='text-align:center'>
                                    <thead class="tbl-header">
                                    <tr>
                                        <th scope="col">Price</th>
                                        <th scope="col">{{$trade_coin}}</th>
                                        <th scope="col">{{$base_coin}}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="tbl-content">
                                    <tr v-for="trade in sell_trades" @click="sell_click(trade)">
                                        <td>@{{printNormal(trade.price)}}</td>
                                        <td>@{{printNormal(trade.amount)}}</td>
                                        <td>@{{ printNormal(trade.price*trade.amount)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr class="hr-buy">
                    </div>
                    <div class="col-sm">
                        <div>
                            <h2>Sell {{$trade_coin}}</h2>
                            <p v-if="wallet_trade!=null">Balance: @{{wallet_trade.balance}} @{{wallet_trade.name}}</p>
                            <form enctype="multipart/form-data" method="POST" ref="sell_form" @submit.prevent="sell()">
                                <div class="form-group">
                                    <label for="sell_amount">Amount</label>
                                    <div class="input-group">
                                        <input id="sell_amount" class="form-control" type="text" name="amount"
                                               v-bind:value="printNormal(sell_trade.amount)" v-on:change="sell_amount()"
                                               ref="sell_amount" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$trade_coin}}</span>
                                        </div>
                                    </div>
                                    <label for="sell_price">Price</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="sell_price" name="price"
                                               v-bind:value="printNormal(sell_trade.price)" v-on:change="sell_price()"
                                               ref="sell_price" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$base_coin}}</span>
                                        </div>
                                    </div>
                                    <label for="sell_sum">Sum</label>
                                    <div class="input-group">
                                        <input id="sell_sum" class="form-control" type="text"
                                               v-bind:value="printNormal(sell_trade.amount_base)" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$base_coin}}</span>
                                        </div>
                                    </div>
                                    <label for="sell_fee">Fee (@{{ fee*100 }}% min:1sat )</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="sell_fee"
                                               v-bind:value="printNormal(sell_trade.amount_base*fee)" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$base_coin}}</span>
                                        </div>
                                    </div>
                                    <label for="sell_price">Total</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="sell_total"
                                               v-bind:value="printNormal(sell_trade.amount_base*minus)"
                                               v-on:change="sell_total()" ref="sell_total">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{$base_coin}}</span>
                                        </div>
                                    </div>
                                    <br>
                                    <button class="btn btn-outline-danger">Sell {{$trade_coin}}</button>
                                </div>
                                <input type="hidden" name="market_id" v-bind:value="market_id">
                                <input type="hidden" name="type" value="sell">
                                {{csrf_field()}}
                            </form>
                        </div>
                        <h2 style='text-align:center'>Buy orders</h2>
                        <div class="table-scroll">
                            <div class="tbl tbl-red">
                                <table class="table table-hover table-bordered buy_table" style='text-align:center'>
                                    <thead class="tbl-header">
                                    <tr>
                                        <th scope="col">Price</th>
                                        <th scope="col">{{$trade_coin}}</th>
                                        <th scope="col">{{$base_coin}}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="tbl-content">
                                    <tr v-for="trade in buy_trades" @click="buy_click(trade)">
                                        <td>@{{printNormal(trade.price)}}</td>
                                        <td>@{{printNormal(trade.amount)}}</td>
                                        <td>@{{ printNormal(trade.price*trade.amount)}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr class="hr-sell">

                    </div>


                </div>
                <a href="{{route('tutorial')}}">Tutorial on deposit,trade,withdrawal</a>
                @if ($meta)
                    <div>
                        <h4>Coin info:</h4>
                        <p><a href="{{$meta->source}}" target="_blank">Source code</a></p>
                        @if($meta->block_explorer)<p><a href="{{$meta->block_explorer}}" target="_blank">Block
                                explorer</a></p>@endif
                        @if($meta->announcement)<p><a href="{{$meta->announcement}}" target="_blank">Announcement</a>
                        </p>@endif

                    </div>
                @endif
                <div v-if="checkObject(market_trades)">
                    <h2 style='text-align:center'>Market Trade History</h2>
                    <div class="table-scroll">
                        <div class="tbl tbl-blue">
                            <table class="table table-hover table-bordered" style='text-align:center'>
                                <thead class="tbl-header">
                                <tr>
                                    <th scope="col">Time</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">{{$trade_coin}}</th>
                                    <th scope="col">{{$base_coin}}</th>
                                </tr>
                                </thead>
                                <tbody class="tbl-content">
                                <tr v-for="trade in market_trades">
                                    <td>@{{printNormal(trade.updated_at)}}</td>
                                    <td>@{{printNormal(trade.type)}}</td>
                                    <td>@{{printNormal(trade.price)}}</td>
                                    <td>@{{printNormal(trade.amount)}}</td>
                                    <td>@{{ printNormal(trade.price*trade.amount)}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div v-if="checkObject(user_trades)">
                    <h2 style='text-align:center'>Your Trade History</h2>
                    <div class="table-scroll">
                        <div class="tbl tbl-lightblue">
                            <table class="table table-hover table-bordered" style='text-align:center'>
                                <thead class="tbl-header">
                                <tr>
                                    <th scope="col">Time</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">{{$trade_coin}}</th>
                                    <th scope="col">{{$base_coin}}</th>
                                </tr>
                                </thead>
                                <tbody class="tbl-content">
                                <tr v-for="trade in user_trades">
                                    <td>@{{printNormal(trade.created_at)}}</td>
                                    <td>@{{printNormal(trade.type)}}</td>
                                    <td>@{{printNormal(trade.price)}}</td>
                                    <td>@{{printNormal(trade.amount)}}</td>
                                    <td>@{{ printNormal(trade.price*trade.amount)}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
@push ('js')
    <script src="{{asset('js/market.js')}}"></script>
    @if ($draw_graph)
        <script src="/js/plotly-finance.min.js"></script>
        <script>
            var _layout = {
                dragmode: 'zoom',
                margin: {
                    r: 90,
                    t: 25,
                    b: 40,
                    l: 90 //80 for 0 - 99, 90 for 100 - 9999, 100 for 10000-99999
                },
                showlegend: false,
                xaxis: {
                    autorange: true,
                    domain: [0, 1],
                    range: [data.x[0], data.x[data.x.length]],
                    rangeslider: {range: [data.x[0], data.x[data.x.length]]},
                    title: 'Date',
                    type: 'date'
                },
                yaxis: {
                    autorange: true,
                    domain: [0, 1],
                    range: [0, 1],
                    type: 'linear'
                }
            };
            Plotly.plot('chart_plotly', [data], _layout);
        </script>
    @endif
@endpush
