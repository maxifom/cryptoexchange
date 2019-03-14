@extends('admin.app')
@section('content_admin')
    <div class="container" style="padding-bottom: 100px;">
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div>
        @endif
        <h1 style="text-align: center;">Coin requests:</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Delete</th>
                        <th>Review</th>
                        <th>Confirm</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($requests as $request)
                        <tr>
                            <td><form action="{{route('admin_request_to_coin')}}" method="POST">
                                    <input type="hidden" value="{{$request->id}}" name="request_id">
                                    @csrf
                                    <button class="btn btn-primary">Request {{$request->id}} to coin </button>
                                </form></td>
                            <td>{{$request->name}}</td>
                            <td>
                                @if ($request->status=='created')
                                    Created
                                @elseif($request->status=='under_review')
                                    Under Review
                                @endif
                            </td>
                            <td>
                                <form action="{{route('admin_delete_request')}}" method="POST">
                                    <button class="btn btn-danger">Delete</button>
                                    <input type="hidden" value="{{$request->id}}" name="request_id">
                                    @csrf
                                </form>
                            </td>
                            <td>
                                <form action="{{route('admin_reviewRequest')}}" method="POST">
                                    <button class="btn btn-primary">Review</button>
                                    <input type="hidden" value="{{$request->id}}" name="request_id">
                                    @csrf
                                </form>
                            </td>
                            <td>
                                <form action="{{route('admin_confirmRequest')}}" method="POST">
                                    <button class="btn btn-primary">Confirm</button>
                                    <input type="hidden" value="{{$request->id}}" name="request_id">
                                    @csrf
                                </form>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
