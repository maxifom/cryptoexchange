@extends('layouts.app') @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 style="text-align: center;">Create support ticket</h1>
                <form action="{{route('createTicketPOST')}}" method="POST">
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input id='subject' type="text" name='subject' class="form-control" placeholder="Subject" required>
                        @if ($errors->has('subject'))
                            <span class="invalid-feedback" style="display: block;">
                                <strong>{{ $errors->first('subject') }}</strong>
                            </span>
                        @endif
                        <div class="form-group">
                            <label for="question">Your question:</label>
                            <textarea class="form-control" id="question" rows="5" name="question"
                                      placeholder="Your question" required></textarea>
                            @if ($errors->has('question'))
                                <span class="invalid-feedback" style="display: block;">
                                <strong>{{ $errors->first('question') }}</strong>
                            </span>
                            @endif
                        </div>
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
                    <button type='submit' class='btn btn-outline-primary'>Create ticket</button>
                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
@endsection