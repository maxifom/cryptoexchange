@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1>New API token</h1>
                <form action="{{route('api_create')}}" method="POST">
                    <div class="form-group">
                        <label>Current IP</label>
                        <input id='current_ip' type="text" value="{{$ip}}"  class="form-control" >
                        <label for="type">Select type</label>
                        <select id='type' class="custom-select" name="type" required>
                            <option selected value="0">Open API only</option>
                            <option value="1">Open+Wallet</option>
                            <option value="2">Open+Trade</option>
                            <option value="3">Open+Wallet+Trade</option>
                        </select>
                        <label for="ip1">IP 1</label>
                        <div class="input-group mb-3">
                            <input id='ip1' type="text" name='ip1' placeholder="IP 1" class="form-control" required>
                        </div>
                        <label for="ip2">IP 2 (optional)</label>
                        <div class="input-group mb-3">
                            <input id='ip2' type="text" name='ip2' placeholder="IP 2" class="form-control">
                        </div>
                        <label for="ip3">IP 3 (optional)</label>
                        <div class="input-group mb-3">
                            <input id='ip3' type="text" name='ip3' placeholder="IP 3" class="form-control">
                        </div>
                        <label for="ip4">IP 4 (optional)</label>
                        <div class="input-group mb-3">
                            <input id='ip4' type="text" name='ip4' placeholder="IP 4" class="form-control">
                        </div>
                        <label for="ip5">IP 5 (optional)</label>
                        <div class="input-group mb-3">
                            <input id='ip5' type="text" name='ip5' placeholder="IP 5" class="form-control">
                        </div>
                    </div>
                    {{csrf_field()}}
                    <button class="btn btn-outline-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection