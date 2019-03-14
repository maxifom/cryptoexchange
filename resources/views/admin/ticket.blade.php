@extends('admin.app') @section('content_admin')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('status'))
                    <div class="alert alert-primary">{{session('status')}}</div>
                @endif
                <h1 style="text-align: center;">Ticket {{$ticket->id}}</h1>
                @if ($ticket->status=='opened')
                    <h4 class="text-primary">Opened</h4>
                @elseif($ticket->status=='answered')
                    <h4 class="text-success">Answered</h4>
                @elseif($ticket->status=='closed')
                    <h4 class="text-danger">Closed</h4>
                @endif
                <h2>Subject: {{$ticket->subject}}</h2>
                <h3>Messages:</h3>
                @foreach($ticket_texts as $text)
                    @if ($text->type=='question')
                        <div class="col">
                            <small class="form-text text-primary">User at {{$text->created_at}}</small>
                            <p style="word-break: break-word">{{$text->text}}</p>
                        </div>
                        <hr>
                    @elseif ($text->type=='answer')
                        <div class="col">
                            <small class="form-text text-danger">Admin at {{$text->created_at}}</small>
                            <p style="word-break: break-word">{{$text->text}}</p>
                        </div>
                        <hr>
                    @endif
                @endforeach
            </div>

        </div>
        @if ($ticket->status!='closed')
            <form action="{{route('admin_answer_ticket')}}" method="POST">
                <div class="form-group">
                    <label for="text">Answer to ticket:</label>
                    <textarea class="form-control" id="text" rows="5" name="text"
                              placeholder="Answer to ticket" required></textarea>
                    @if ($errors->has('text'))
                        <span class="invalid-feedback" style="display: block;">
                                <strong>{{ $errors->first('text') }}</strong>
                            </span>
                    @endif
                </div>
                <button type='submit' class='btn btn-outline-primary'>Add to ticket</button>
                <input type="hidden" value="{{$ticket->id}}" name="ticket_id">
                {{csrf_field()}}
            </form>
        @else
            <h5 class="text-danger">You cannot add to closed ticket</h5>
        @endif
    </div>
@endsection
@push ('css')
    <style>
        hr {
            border-top: 2px dashed black;
        }
    </style>
@endpush