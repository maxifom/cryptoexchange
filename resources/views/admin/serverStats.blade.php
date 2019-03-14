@extends('admin.app')
@section('content_admin')
    <div class="container" style="padding-bottom: 100px;">
        <h1 style="text-align: center;">Server stats:</h1>
        <h1 style="text-align: center;">CPU</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Type</th>
                        <th>Value</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    <tr>
                        <td>Average now</td>
                        <td>{{$stats['cpu']['average_now']}}%</td>
                    </tr>
                    <tr>
                        <td>Average 2 days</td>
                        <td>{{$stats['cpu']['average_two_days']}}%</td>
                    </tr>
                    <tr>
                        <td>Maximum 2 days</td>
                        <td>{{$stats['cpu']['maximum_two_days']}}%</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <h1 style="text-align: center;">Memory (RAM)</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Type</th>
                        <th>Value</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    <tr>
                        <td>Used</td>
                        <td>{{$stats['memory']['used']}} GB</td>
                    </tr>
                    <tr>
                        <td>Free</td>
                        <td>{{$stats['memory']['free']}} GB</td>
                    </tr>
                    <tr>
                        <td>Total memory</td>
                        <td>{{$stats['memory']['total_memory']}} GB</td>
                    </tr>
                    <tr>
                        <td>Max used 2 days</td>
                        <td>{{$stats['memory']['max_used_two_days']}} GB</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <h1 style="text-align: center;">Disk</h1>
        <div class="table-scroll" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Type</th>
                        <th>Value</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    <tr>
                        <td>Used</td>
                        <td>{{$stats['disk']['used']}} GB</td>
                    </tr>
                    <tr>
                        <td>Free</td>
                        <td>{{$stats['disk']['free']}} GB</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <h1 style="text-align: center;">Processes</h1>
        <div class="" style="overflow-x: hidden;">
            <div class="tbl tbl-blue">
                <table class="table table-responsive-sm" style="text-align: center;">
                    <thead class="tbl-header">
                    <tr>
                        <th>Process</th>
                        <th>Enabled</th>
                    </tr>
                    </thead>
                    <tbody class="tbl-content">
                    @foreach ($stats['processes'] as $process)
                        <tr>
                            <td>{{$process['name']}}</td>
                            <td>
                                @if($process['enabled'])
                                    <span class="text-success">ENABLED</span>
                                @else
                                    <span class="text-danger">DISABLED</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
