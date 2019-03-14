@extends('layouts.app') @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 style="text-align: center;">Withdraw {{$coin->name}}</h1>
                <form action="{{route('withdrawPost')}}" method="POST">
                    <div class="form-group">
                        <label for="name">Coin</label>
                        <input id='name' type="text" name='name' value='{{$coin->name}}' class="form-control" readonly>
                        <label for="balance">Balance</label>
                        <div class="input-group mb-3">
                            <input id='balance' type="text" class="form-control" placeholder='{{$wallet->balance}}'
                                   readonly aria-describedby="coin_name_addon_balance">
                            <div class="input-group-append">
                                <span class="input-group-text" id="coin_name_addon_balance">{{$coin->name}}</span>
                            </div>
                        </div>
                        <label for="address">Address</label>

                        @if ($errors->has('address'))
                            <input id='address' type="text" name='address' class="form-control is-invalid" required
                                   value="{{old('address')}}">
                            <span class="invalid-feedback">
                        <strong>{{ $errors->first('address') }}</strong>
                    </span>
                        @else
                            <input id='address' type="text" name='address' class="form-control" required
                                   value="{{old('address')}}"> @endif
                        <label for="value">Value</label>
                        <div class="input-group mb-3">
                            @if ($errors->has('value'))
                                <input id='value' type="text" name='value' class="form-control is-invalid"
                                       aria-describedby="coin_name_addon" required value="{{old('value')}}">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="coin_name_addon">{{$coin->name}}</span>
                                </div>
                                <span class="invalid-feedback">
                            <strong>{{ $errors->first('value') }}</strong>
                        </span>
                            @else
                                <input id='value' type="text" name='value' class="form-control"
                                       aria-describedby="coin_name_addon" required value="{{old('value')}}">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="coin_name_addon">{{$coin->name}}</span>
                                </div>
                            @endif

                        </div>
                        @if ($errors->has('value')) @endif
                        <small id="emailHelp" class="form-text text-muted">Minimal
                            withdrawal: @php echo number_format($fee*1.5,8,'.','')@endphp {{$coin->name}}</small>
                        <label for="fee">Fee</label>

                        <div class="input-group mb-3">
                            <input id='fee' type="text" class="form-control" placeholder="{{$fee}}" readonly
                                   aria-describedby="coin_name_addon_fee">
                            <div class="input-group-append">
                                <span class="input-group-text" id="coin_name_addon_fee">{{$coin->name}}</span>
                            </div>
                        </div>
                        <label for="final_value">Total to withdraw</label>

                        <div class="input-group mb-3">
                            <input id='final_value' type="text" class="form-control" placeholder="0"
                                   aria-describedby="coin_name_addon_final" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text" id="coin_name_addon_final">{{$coin->name}}</span>
                            </div>
                        </div>
                    </div>

                    <button type='submit' class='btn btn-outline-primary'>Withdraw</button>

                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
@endsection @push ('js')
    <script>
        $("#value").on('change', function () {
            if ($(this).val() >= {{ $fee * 1.5 }})
                $("#final_value").attr('placeholder', ($(this).val() - parseFloat($("#fee").attr("placeholder"))).toFixed(8) + " {{$coin->name}}");
        });
        $("#value").on('keyup', function () {
            if ($(this).val() >= {{ $fee * 1.5 }})
                $("#final_value").attr('placeholder', ($(this).val() - parseFloat($("#fee").attr("placeholder"))).toFixed(8) + " {{$coin->name}}");
        });
    </script> @endpush