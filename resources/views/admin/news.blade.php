@extends('admin.app') @section('content_admin')
    <div class="container">
        <div class="row justify-content-center" style="padding-bottom: 20px;">
            <div class="col-md-12">
                @if (session('status'))
                    <div class="alert alert-primary">{{session('status')}}</div>
                @endif
                <h1 style="text-align: center;">News</h1>
                @foreach($news as $new)
                    <div class="col">
                        <h6  style="float:right">{{$new->created_at}}</h6>

                        <form action="{{route('admin_change_news')}}" method="POST">
                            <div class="form-group">
                                <label for="header">Header</label>
                                <input id="header" class="form-control" type="text" name="header" placeholder="Header"
                                       value="{{$new->header}}">
                            </div>
                            <div class="form-group">
                                <label for="text">Text</label>
                                <textarea name="text" class="form-control" id="text" cols="30" rows="10"
                                          placeholder="Text" style="resize:vertical">{{$new->text}}</textarea>
                            </div>
                            <input type="hidden" value="{{$new->id}}" name="new_id">
                            @csrf
                            <button class="btn btn-primary" type="submit">Change</button>
                        </form>

                    </div>
                    <hr>
                @endforeach
            </div>
            @if ($page!=1)
                <a class='btn btn-primary' href="{{route('admin_news',1)}}">First page</a>
                <a class='btn btn-primary' style="margin-left: 10px;" href="{{route('admin_news',$page-1)}}">Prev
                    page</a>
            @endif
            <button class="btn btn-primary" style="margin-left: 10px;margin-right: 10px;">{{$page}}</button>
            @if ($pages!=$page)
                <a class='btn btn-primary' style="margin-right: 10px;" href="{{route('admin_news',$page+1)}}">Next
                    page</a>
                <a class='btn btn-primary' href="{{route('admin_news',$pages)}}">Last page</a>
            @endif
        </div>
    </div>
@endsection
@push ('css')
    <style>
        hr {
            border-top: 2px dashed black;
        }
    </style>
@endpush