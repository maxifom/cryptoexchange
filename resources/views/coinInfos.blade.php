@extends ('admin.app') @section ('content_admin')
    <br>
    <div class="container" id="coinInfos">
        <div class="row">
            <div class="tbl tbl-blue">
                <table class='table table-responsive-sm'>
                    <thead>
                    <th>Name</th>
                    <th>Block count</th>
                    <th>Last block</th>
                    <th>Time</th>
                    <th>Connections</th>
                    </thead>
                    <tbody>
                    @foreach ($coinInfos as $info)
                        <tr id="{{$info['name']}}">
                            <td id="{{$info['name']}}-name">{{$info['name']}}</td>
                            <td id="{{$info['name']}}-block_count">{{$info['block_count']}}</td>
                            <td id="{{$info['name']}}-last_block">{{$info['last_block']}}</td>
                            <td id="{{$info['name']}}-block_time">
                                {{$info['block_time']}}
                            </td>
                            <td id="{{$info['name']}}-connections">{{$info['connections']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection 
