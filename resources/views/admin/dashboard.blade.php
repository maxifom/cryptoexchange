@extends ('admin.app') @section ('content_admin')
    <div class="container">
        <h1 style="text-align: center;margin-bottom: 30px;">Admin dashboard</h1>
        <h1>Balances:</h1>
        @foreach ($balances as $balance)
            <p>{{$balance['balance']}} {{$balance['name']}} ({{$balance['user_balance']}} {{$balance['name']}}
                ) {{$balance['profit']}} {{$balance['name']}}</p>
        @endforeach
    </div>
@endsection