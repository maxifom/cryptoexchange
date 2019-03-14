@extends('layouts.app') @section('content')
    <div class="container">
        <div class="row justify-content-center" style="padding-bottom: 20px;">
            <div class="col-md-12">
                <h1 style="text-align: center;">News <i class="fas fa-newspaper"></i></h1>
                @foreach($news as $new)
                <small style="float:right;margin-right: 5px;">{{$new->created_at}}</small>
                    <div style="border: 1px solid #d3d3d3;padding-left:10px;padding-right:10px;margin-bottom: 10px;">
                        <h4>{{$new->header}}</h4>
                        <pre>{{$new->text}}</pre>
                    </div>
                @endforeach
            </div>
            @if ($page!=1)
                <a class='btn btn-primary' href="{{route('news',1)}}"><i class="fas fa-angle-double-left"></i></a>
                <a class='btn btn-primary' style="margin-left: 10px;" href="{{route('news',$page-1)}}"><i
                            class="fas fa-angle-left"></i>
                </a>
            @endif
            @if($pages!=1)
                <button class="btn btn-primary" style="margin-left: 10px;margin-right: 10px;">{{$page}}</button>
            @endif
            @if ($pages!=$page)
                <a class='btn btn-primary' style="margin-right: 10px;" href="{{route('news',$page+1)}}"><i
                            class="fas fa-angle-right"></i></a>
                <a class='btn btn-primary' href="{{route('news',$pages)}}"><i class="fas fa-angle-double-right"></i></a>
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
