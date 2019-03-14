@extends ('layouts.app') @section ('content')
<br>
<div class="container" id="coinInfos">
    <div class="row justify-content-center">
        <table class='table table-responsive table-striped'>
            <thead>
                <th>Name</th>
                <th>Block count</th>
                <th>Last block</th>
                <th>Time</th>
                <th>Connections</th>                               
            </thead>
            <tbody>
                @foreach ($coinInfos as $info)
                <tr id="{{$info->coin->name}}">
                    <td id="{{$info->coin->name}}-name">{{$info->coin->name}}</td>
                    <td id="{{$info->coin->name}}-block_count">{{$info->block_count}}</td>
                    <td id="{{$info->coin->name}}-last_block">{{$info->last_block}}</td>
                    <td id="{{$info->coin->name}}-block_time">
                        {{$info->block_time}}
                    </td>
                    <td id="{{$info->coin->name}}-last_block">{{$info->connections}}</td> 
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection @section('scripts')
</script>
<script src="{{asset('js/coinInfos.js')}}"></script>
@endsection