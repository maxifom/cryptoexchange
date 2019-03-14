@extends('admin.app')
@section('content_admin')
    <div class="container" style="padding-bottom: 100px;">
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div>
        @endif
        <h1 style="text-align: center;">Support tickets:</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Ticket</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Last update</th>
                        <th>Close</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($tickets as $ticket)
                        <tr>
                            <td><a href="{{route('admin_ticket',$ticket->id)}}">{{$ticket->id}}</a></td>
                            <td>{{$ticket->subject}}</td>
                            <td>{{$ticket->status}}</td>
                            <td>{{$ticket->updated_at}}</td>
                            <td>
                                <form action="{{route('admin_close_ticket')}}" method="POST">
                                    <button type='submit' class="btn btn-danger">Close</button>
                                    <input type="hidden" value="{{$ticket->id}}" name="ticket_id">
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
