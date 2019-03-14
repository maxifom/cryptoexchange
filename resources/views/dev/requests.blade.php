@extends('layouts.app')
@section('content')
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
                        <th>Name</th>
                        <th>Status</th>
                        <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($requests as $request)
                        <tr>
                            <td>{{$request->name}}</td>
                            <td>
                                @if ($request->status=='created')
                                    Created
                                @elseif($request->status=='under_review')
                                    Under Review
                                @else
                                    Confirmed
                                @endif
                            </td>
                            <td>
                                <form action="{{route('dev_request_delete')}}" method="POST">
                                    <button class="btn btn-danger" @if($request->status!='created') disabled @endif> Delete</button>
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
        @if($count==0)
            <a class="btn btn-primary" href="{{route('dev_request_form')}}">Request coin</a>
        @endif
    </div>
@endsection
