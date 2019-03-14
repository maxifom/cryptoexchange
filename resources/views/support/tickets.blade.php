@extends ('layouts.app') @section ('content')
    <br>
    <div class="container">
        @if (session('status'))
            <div class="alert alert-primary">{{session('status')}}</div>
        @endif
        <h1 style="text-align: center;">Support Tickets:</h1>
        <div class="row justify-content-center">
            <div class="tbl tbl-blue" style="width:100%;max-width: 100%;">
                <table class='table table-responsive-sm'>
                    <thead>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Last update</th>
                    </thead>
                    <tbody>
                    @foreach ($tickets as $ticket)
                        <tr>
                            <td><a href="{{route('ticket',$ticket->id)}}">{{$ticket->id}}</a></td>
                            <td>{{$ticket->subject}}</td>
                            <td>
                                @if ($ticket->status=='opened')
                                    <span class="text-primary">Opened</span>
                                @elseif($ticket->status=='answered')
                                    <span class="text-success">Answered</span>
                                @elseif($ticket->status=='closed')
                                    <span class="text-danger">Closed</span>
                                @endif
                            </td>
                            <td>{{$ticket->updated_at}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if ($page!=1)
                <a class='btn btn-primary' href="{{route('supportTickets',1)}}"><i class="fas fa-angle-double-left"></i></a>
                <a class='btn btn-primary' style="margin-left: 10px;" href="{{route('supportTickets',$page-1)}}"><i class="fas fa-angle-left"></i>
                </a>
            @endif
            @if ($pages==0)
            <button class="btn btn-primary" style="margin-left: 10px;margin-right: 10px;">{{$page}}</button>
            @endif
            @if ($pages!=$page)
                <a class='btn btn-primary' style="margin-right: 10px;" href="{{route('supportTickets',$page+1)}}"><i class="fas fa-angle-right"></i></a>
                <a class='btn btn-primary' href="{{route('supportTickets',$pages)}}"><i class="fas fa-angle-double-right"></i></a>
            @endif
        </div>
            <br>
        <div style="text-align: center;">
            <a class='btn btn-primary' href="{{route('createTicket')}}">Create ticket</a>
        </div>
            <a href="{{route('tutorial')}}">Tutorial on deposit,trade,withdrawal</a>
            <p><a href="https://discord.gg/w9BQsun" target="_blank">Discord group</a> for support
            </p>
    </div>
@endsection
