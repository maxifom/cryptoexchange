@extends('admin.app')
@section('content_admin')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1>Add coin form</h1>
                <form action="{{route('addCoin')}}" method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <div class="input-group mb-3">
                            <input id='name' type="text" name='name' placeholder="Coin name" class="form-control" required>
                        </div>
                        <label for="username">Username</label>
                        <div class="input-group mb-3">
                            <input id='username' type="text" name='username' value="user" placeholder="RPC username" class="form-control" required>
                        </div>
                        <label for="password">Password</label>
                        <div class="input-group mb-3">
                            <input id='password' type="text" name='password' value="pass" placeholder="RPC password" class="form-control" required>
                        </div>
                        <label for="port">Port</label>
                        <div class="input-group mb-3">
                            <input id='port' name='port' placeholder="RPC port" value="50000" type="number" class="form-control" required>
                        </div>
                        <label for="needed_confirmations">Needed confirmations</label>
                        <div class="input-group mb-3">
                            <input id='needed_confirmations' value="6" type="number" name='needed_confirmations' placeholder="Needed confirmations for coin" class="form-control" required>
                        </div>
                        <label for="coinbase_maturity">Coinbase maturity</label>
                        <div class="input-group mb-3">
                            <input id='coinbase_maturity' value="20" type="number" name='coinbase_maturity' placeholder="Coinbase maturity for coin" class="form-control" required>
                        </div>
                        <label for="fee">Fee</label>
                        <div class="input-group mb-3">
                            <input id='fee' value="0.00000001" step="0.00000001" type="number" name='fee' placeholder="Fee" class="form-control" required>
                        </div>
                        <label for="trading_fee">Trading fee</label>
                        <div class="input-group mb-3">
                            <input id='trading_fee' type="number" value="0.002" step="0.001" name='trading_fee' placeholder="Trading fee" class="form-control" required>
                        </div>
                        <label for="source">Source code</label>
                        <div class="input-group mb-3">
                            <input id='source' type="text" value="https://github.com" name='source' placeholder="Source code link" class="form-control" required>
                        </div>
                        <label for="block_explorer">Block explorer</label>
                        <div class="input-group mb-3">
                            <input id='block_explorer' type="text" name='block_explorer' placeholder="Block explorer link" class="form-control">
                        </div>
                        <label for="announcement">Announcement</label>
                        <div class="input-group mb-3">
                            <input id='announcement' type="text" name='announcement' placeholder="Announcement link" class="form-control">
                        </div>
                        <label for="type">Type</label>
                        <div class="input-group mb-3">
                            <input id='type' type="text" name='type' value="PoW" placeholder="Type (PoS or PoW)" class="form-control" required>
                        </div>
                    </div>
                    {{csrf_field()}}
                    <button class="btn btn-outline-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection