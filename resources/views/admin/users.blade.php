@extends('admin.app')
@section('content_admin')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div>
        @endif
        <h1 style="text-align: center;">Users:</h1>
        <div class="tbl tbl-blue">
            <table class="table table-responsive-sm" style="text-align: center;">
                <thead class="tbl-header">
                <tr>
                    <th>User id</th>
                    <th>User email</th>
                    <th>Dev</th>
                    <th>Registered</th>
                </tr>
                </thead>
                <tbody class="tbl-content">
                @foreach ($users as $user)
                    <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->email}}</td>
                        <td>@if ($user->dev===1)
                                <button class="btn btn-success">Developer</button>
                            @else
                                <form action="{{route('admin_make_dev')}}" method="post">
                                    <input type="hidden" value="{{$user->id}}" name="user_id">
                                    @csrf
                                    <button class="btn btn-primary">Make Dev</button>
                                </form>
                            @endif
                        </td>
                        <td>{{$user->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
