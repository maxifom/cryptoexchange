@extends('layouts.app')
@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <h1 style="text-align: center;">Your tokens:</h1>
        <div class="tbl tbl-blue">
            <table class="table table-responsive-sm">
                <thead class="tbl-header">
                <tr>
                    <th>Token</th>
                    <th>Trade enabled</th>
                    <th>Wallet enabled</th>
                    <th>IPs</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody class="tbl-content">
                @foreach ($tokens as $token)
                    <tr>
                        <td>{{$token->token}}</td>
                        <td>@if ($token->trade) ENABLED @else DISABLED @endif</td>
                        <td>@if($token->wallet) ENABLED @else DISABLED @endif</td>
                        <td>@foreach ($token->ips as $ip)
                                {{long2ip($ip->api_ip)}}&nbsp;
                            @endforeach
                        </td>
                        <td>
                            <form action="{{route('api_delete')}}" method="post">
                                <button type="submit" class="btn btn-danger">Delete</button>
                                <input type="hidden" name="token_token" value="{{$token->token}}">
                                @csrf
                            </form></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
            <a class="btn btn-primary" href="{{route('api_create_view')}}">Create token</a>
    </div>
@endsection


