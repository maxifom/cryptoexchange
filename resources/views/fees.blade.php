@extends('layouts.app')
@section('content')
    <div class="container" style="text-align: center">
        <h1>Fees:</h1>
        <div class="tbl tbl-blue">
            <table class="table table-responsive-sm">
                <thead class="tbl-header">
                <th>Name</th>
                <th>Withdrawal fee</th>
                <th>Trading fee</th>
                </thead>
                <tbody class="tbl-content">
                @foreach ($coins as $coin)

                    <tr>
                        <td>{{$coin->name}}</td>
                        <td>{{$coin->fee->fee}}</td>
                        <td>{{$coin->trading_fee->fee*100}}%</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
