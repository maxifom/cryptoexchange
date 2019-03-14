@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1>Request coin form</h1>
                <form action="{{route('dev_request_coin')}}" method="POST">
                    <div class="form-group">
                        <label for="name">Short name (like BTC, LTC ...)</label>
                        <div class="input-group mb-3">
                            <input id='name' type="text" name='name' placeholder="Coin name" class="form-control" required>
                        </div>
                        <label for="source">Source code</label>
                        <div class="input-group mb-3">
                            <input id='source' type="text" placeholder="https://github.com" name='source' class="form-control" required>
                        </div>
                        <label for="block_explorer">Block explorer (optional)</label>
                        <div class="input-group mb-3">
                            <input id='block_explorer' type="text" name='block_explorer' placeholder="Block explorer link" class="form-control">
                        </div>
                        <label for="announcement">Announcement</label>
                        <div class="input-group mb-3">
                            <input id='announcement' type="text" name='announcement' placeholder="Announcement link" class="form-control">
                        </div>
                        <label for="type">Type</label>
                        <div class="input-group mb-3">
                            <input id='type' type="text" name='type' placeholder="Type (PoS or PoW)" class="form-control" required>
                        </div>
                        <label for="needed_confirmations">Needed confirmations</label>
                        <div class="input-group mb-3">
                            <input id='needed_confirmations' type="number" name='needed_confirmations' placeholder="Needed confirmations for coin" class="form-control" required>
                        </div>
                    </div>
                    {{csrf_field()}}
                    <button class="btn btn-outline-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection