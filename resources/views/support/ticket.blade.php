@extends('layouts.app') @section('content')
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
                            <small class="form-text text-primary">You at {{$text->created_at}}</small>
                            <p style="word-break: break-word">{{$text->text}}</p>
                        </div>
                        <hr>
                    @elseif ($text->type=='answer')
                        <div class="col">
                            <small class="form-text text-danger">Support at {{$text->created_at}}</small>
                            <p style="word-break: break-word">{{$text->text}}</p>
                        </div>
                        <hr>
                    @endif
                @endforeach
            </div>

        </div>
        @if ($ticket->status!='closed')
            <form action="{{route('addToTicket')}}" method="POST">
                <div class="form-group">
                    <label for="text">Add text to ticket:</label>
                    <textarea class="form-control" id="text" rows="5" name="text"
                              placeholder="Add text" required></textarea>
                    @if ($errors->has('text'))
                        <span class="invalid-feedback" style="display: block;">
                                <strong>{{ $errors->first('text') }}</strong>
                            </span>
                    @endif
                </div>
                @captcha
                <input type="text" class="form-control" style="margin-top: 5px; width:50%;" id="captcha" name="captcha">
                @if ($errors->has('captcha'))
                    <span class="invalid-feedback" style="display: block;">
                                            <strong>{{ $errors->first('captcha') }}</strong>
                        {{--<strong>Invalid captcha</strong>--}}
                                    </span>
                @endif
                <br>
                <button type='submit' class='btn btn-outline-primary'>Add to ticket</button>
                <input type="hidden" value="{{$ticket->id}}" name="ticket_id">
                {{csrf_field()}}
            </form>
            <br>
            <hr class="red-hr">
            <h4>Close ticket</h4>
            <form action="{{route('closeTicket')}}" method="POST">
                <button type='submit' class='btn btn-danger'>Close ticket</button>
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

        .red-hr {
            border-top: 2px dashed red
        }
    </style>
@endpush