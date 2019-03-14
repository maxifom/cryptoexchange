@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Tutorial</h1>
        <h3>How to trade on theBro?</h3>
        <p>To trade on theBro you need to make following steps:</p>
        <ol>
            <li>
                Make sure you have balance in coin, which you want to trade
            </li>
            <li>
                Go to the coin market (<a href="{{route('markets')}}">Markets</a>)
            </li>
            <li>
                There are 2 options, which are made automatically when you click "Buy" or "Sell", for trade:
                <ol>
                    <li>
                        You create a trade: amount and price (If there are no matching trades)
                    </li>
                    <li>
                        You fulfill existing trades for given amount, then price is the maximum/minimum (buy/sell)
                        allowed (If there are trades matching you maximum/minimum price)
                    </li>
                </ol>
            </li>
        </ol>
        <h3>How to deposit to the wallet on theBro?</h3>
        <p>To deposit on theBro you need to make following steps:</p>
        <ol>
            <li>
                Open <a href="{{route('wallets')}}">Wallets</a> tab
            </li>
            <li>
                Find coin you want to deposit
            </li>
            <li>
                Press create to create a depositing address
            </li>
            <li>Deposit coins to this address</li>
            <li>Deposit will be automatically accounted, once the needed confirmations reached</li>
            <li>If the deposit didn't appear in the deposit history for 3-4 hours, please open support ticket</li>
        </ol>
        <h3>How to withdraw from the wallet on theBro?</h3>
        <p>To withdraw from theBro you need to make following steps:</p>
        <ol>
            <li>
                Open <a href="{{route('wallets')}}">Wallets</a> tab
            </li>
            <li>
                Find coin you wish to withdraw
            </li>
            <li>
                Press withdraw near coin name
            </li>
            <li>Input address and amount to withdraw</li>
            <li>Confirm your withdrawal by email</li>
        </ol>
    </div>

@endsection