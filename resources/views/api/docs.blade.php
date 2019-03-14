@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 style="text-align: center;">API documentation:</h1>
        <h5>API endpoint: {{env("APP_URL")}}/api</h5>
        <h5>All API calls are made in POST and returns JSON as a response</h5>
        <h5>API throttle: 120requests/minute</h5>
        <a class='btn btn-primary' href="{{route('api_tokens')}}" style="margin-bottom: 10px;">Your tokens</a>
        <div class="api-section">
            <h2 class="api-section-name">Open API</h2>
            <div class="api">
                <div class="api-method"><span>getCoins</span> ({{env("APP_URL")}}/api/getCoins)</div>
                <div class="api-description">Returns all active coins</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[
                        <br> #array of all coins#
                        <br>]
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">No errors</p>
                </div>
            </div>
            <div class="api">
                <div class="api-method"><span>getMarket</span> ({{env("APP_URL")}}/api/getMarket)</div>
                <div class="api-description">Returns info about market by name or id</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-required">market_id / market_name - Market id or name (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":{
                        <br> #info about market#
                        <br> }, "error":false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {
                        "data":null, "error":true
                        }
                    </p>
                </div>
            <div class="api">
                <div class="api-method"><span>getMarkets</span> ({{env("APP_URL")}}/api/getMarkets)</div>
                <div class="api-description">Returns all markets</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[
                        <br> #array of all markets#
                        <br> ]
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">No errors</p>
                </div>
            </div>

            </div>
            <div class="api">
                <div class="api-method"><span>getMarketTrades</span> ({{env("APP_URL")}}/api/getMarketTrades)</div>
                <div class="api-description">Returns 50 current market trades of certain type</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-required">type - Type of trades (sell/buy) (required)</p>
                    <p class="param-required">market_id / market_name - Market id or name (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[
                        <br>#array of trades#
                        <br> ], "error":false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {
                        "data":null, "error":true
                        }
                    </p>
                </div>
            </div>
            <div class="api">
                <div class="api-method"><span>getLastMarketTrades</span> ({{env("APP_URL")}}/api/getLastMarketTrades)
                </div>
                <div class="api-description">Returns 200 past market trades</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-required">market_id / market_name - Market id or name (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[
                        <br> #array of trades#
                        <br> ], "error":false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {
                        "data":null, "error":true
                        }
                    </p>
                </div>
            </div>

        </div>


        <div class="api-section">
            <h2 class="api-section-name">Trade API</h2>
            <div class="api">
                <div class="api-method"><span>getTradeHistory</span> ({{env("APP_URL")}}/api/getTradeHistory)</div>
                <div class="api-description">Returns your trade history paged in 100 batches</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-optional">page - Page to view (optional)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[
                        <br>#array of trades#
                        <br> ],
                        <br> "pages": #number of pages,
                        <br> "page": #current page,
                        <br> "timezone": #your timezone,
                        <br> "error": false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {"data":null,
                        "error": true
                        }</p>
                </div>
            </div>
            <div class="api">
                <div class="api-method"><span>getOpenTrades</span> ({{env("APP_URL")}}/api/getOpenTrades)</div>
                <div class="api-description">Returns your open trades paged in 100 batches</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-optional">page - Page to view (optional)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[
                        <br>#array of trades#
                        <br>],
                        <br>"pages": #number of pages,
                        <br>"page": #current page,
                        <br>"timezone": #your timezone,
                        <br>"error": false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {"data":null,
                        "error": true
                        }</p>
                </div>
            </div>
            <div class="api">
                <div class="api-method"><span>trade</span> ({{env("APP_URL")}}/api/trade)</div>
                <div class="api-description">Trade certain amount or create a new trade with this amount if no matching
                    trades found
                </div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-required">price - Trade price (required) </p>
                    <p class="param-required">amount - Trade amount (required)</p>
                    <p class="param-required">market_id / market_name - market id or name (required)</p>
                    <p class="param-required">type - Trade type (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data": {
                        <br>"fulfilled_amount": #amount of trade that been matched with existed trades,
                        <br>"created_trade": { #trade created in case fulfulled amount is less than amount
                        <br>"id": #trade_id,
                        <br>"amount": #amount of trade,
                        <br>"price": #price of trade,
                        <br>"type": #type of trade,
                        <br>"market": #trade market name,
                        <br>"market_id": #trade market id
                        <br>}
                        <br>},
                        <br>"error": false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {
                        "data":null, "error":true
                        }
                    </p>
                </div>
            </div>
            <div class="api">
                <div class="api-method"><span>closeTrade</span> ({{env("APP_URL")}}/api/closeTrade)</div>
                <div class="api-description">Close existing trade by id</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-required">trade_id - ID of trade to close (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":"success", <br>"error":false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {
                        "data":null, "error":true
                        }
                    </p>
                </div>
            </div>


        </div>

        <div class="api-section">
            <h2 class="api-section-name">Wallet API</h2>
            <div class="api">
                <div class="api-method"><span>getDepositHistory</span> ({{env("APP_URL")}}/api/getDepositHistory)</div>
                <div class="api-description">Returns your deposit history paged in 100 batches</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-optional">page - Page to view (optional)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[
                        <br>#array of deposits#
                        <br> ],
                        <br> "pages": #number of pages,
                        <br> "page": #current page,
                        <br> "timezone": #your timezone,
                        <br> "error": false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {"data":null,
                        "error": true
                        }</p>
                </div>
            </div>
            <div class="api">
                <div class="api-method"><span>getWithdrawalHistory</span> ({{env("APP_URL")}}/api/getWithdrawalHistory)
                </div>
                <div class="api-description">Returns your withdrawal history paged in 100 batches</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-optional">page - Page to view (optional)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[
                        <br>#array of withdrawals#
                        <br>],
                        <br>"pages": #number of pages,
                        <br>"page": #current page,
                        <br>"timezone": #your timezone,
                        <br>"error": false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {"data":null,
                        "error": true
                        }</p>
                </div>
            </div>
            <div class="api">
                <div class="api-method"><span>getWallets</span> ({{env("APP_URL")}}/api/getWallets)</div>
                <div class="api-description">Get all wallets</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data": [#array of wallets#]</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        No error
                    </p>
                </div>
            </div>
            <div class="api">
                <div class="api-method"><span>withdraw</span> ({{env("APP_URL")}}/api/withdraw)</div>
                <div class="api-description">Withdraw coin to address</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-required">coin_id or coin_name - Coin id or name (required)</p>
                    <p class="param-required">value - Value of withdrawal (required)</p>
                    <p class="param-required">address - Address to withdraw (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":"success", <br>"error":false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {
                        "data":null, "error":true
                        }
                    </p>
                </div>
            </div>

            <div class="api">
                <div class="api-method"><span>createWallet</span> ({{env("APP_URL")}}/api/createWallet)</div>
                <div class="api-description">Create new deposit address for coin</div>
                <div class="api-params">
                    Params:
                    <p class="param-required">token - An API token (required)</p>
                    <p class="param-required">coin_id or coin_name - Coin id or name (required)</p>
                </div>
                <div class="api-result">
                    Result:
                    <p class="result">{
                        "data":[ <br>
                        "address":#deposit address,
                        <br>"coin_name":#coin name
                        <br>"coin_id":#coin id
                        <br>],
                        <br>"error":false
                        }</p>
                </div>
                <div class="api-error">
                    Errors:
                    <p class="error">
                        {
                        "data":null, "error":true
                        }
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection


@push ('css')
    <style>
        .api-section {
            margin-bottom: 50px;
            display: block;
            border-bottom: 1px solid gray;
            border-top: 1px solid gray;
        }

        .api-section-name {
            font-weight: bold;
        }

        .api-method {
            font-weight: bold;
        }

        .api-method span {
            color: #0f6ecd;
        }

        .param-required {
            font-weight: bold;
        }

        .param-optional {
            font-weight: lighter;
        }

        .error {
            color: #b21f2d;
        }

        .api {
            border-bottom: 1px dashed #383d41;
            margin-bottom: 10px;
        }

        .api:last-child {
            border-bottom: none;
        }
    </style>
@endpush