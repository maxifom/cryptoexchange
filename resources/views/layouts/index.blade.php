@extends('layouts.app')
    @section ('title')
    <title>Exchange SCORE/BTC 0.00000070</title>
    @endsection
    @section ('styles')
    <style>
        .hr-buy {
            border-top: 3px dashed #28a745;
        }

        .hr-sell {
            border-top: 3px dashed #dc3545;
        }
    </style>
    @endsection
    @section('content')
    <div class="container-fluid">
        <h1 style='text-align:center'>SCORE/BTC</h1>
        <div class="row">
            <div style='width:100%;height:300px;background:lightblue'>CHARTS</div>
        </div>
        <div class="row">
            <div class="col-sm">
                <div>
                    <h1>Buy SCORE</h1>
                    <form action="">
                        <div class="form-group">
                            <label for="buy_amount">Amount</label>
                            <input id="buy_amount" class="form-control" type="text">
                            <label for="buy_price">Price</label>
                            <input type="text" class="form-control" id="buy_price">
                            <p style="font-weight:bold">Total:
                                <span id="buy_total">0</span> BTC Fee:(
                                <span id="buy_fee">0</span> BTC )</p>
                            <button class="btn btn-outline-success">Buy SCORE</button>
                        </div>
                    </form>
                </div>
                <h1 style='text-align:center'>Sell orders</h1>
                <table class="table table-hover sell_table" style='text-align:center'>
                    <thead>
                        <tr>
                            <th scope="col">Price</th>
                            <th scope="col">SCORE</th>
                            <th scope="col">BTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr price="0.00000057" amount="20000.00000000">
                            <td>0.00000057</td>
                            <td>20000.00000000</td>
                            <td>0.01140000</td>
                        </tr>
                        <tr price="0.00000058" amount="20000.00000000">
                            <td>0.00000058</td>
                            <td>20000.00000000</td>
                            <td>0.01140000</td>
                        </tr>
                        <tr price="0.00000059" amount="20000.00000000">
                            <td>0.00000059</td>
                            <td>20000.00000000</td>
                            <td>0.01140000</td>
                        </tr>
                    </tbody>
                </table>
                <hr class="hr-buy">
            </div>
            <div class="col-sm">
                <div>
                    <h1>Sell SCORE</h1>
                    <form action="">
                        <div class="form-group">
                            <label for="sell_amount">Amount</label>
                            <input id="sell_amount" class="form-control" type="text">
                            <label for="sell_price">Price</label>
                            <input class="form-control" id="sell_price" type="text">
                            <p style="font-weight:bold">Total:
                                <span id="sell_total">0</span> BTC Fee:(
                                <span id="sell_fee">0</span> BTC )</p>
                            <button class="btn btn-outline-danger">Sell SCORE</button>
                        </div>
                    </form>
                </div>
                <h1 style='text-align:center'>Buy orders</h1>
                <table class="table table-hover buy_table" style='text-align:center'>
                    <thead>
                        <tr>
                            <th scope="col">Price</th>
                            <th scope="col">SCORE</th>
                            <th scope="col">BTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr price="0.00000057" amount="20000.00000000">
                            <td>0.00000057</td>
                            <td>20000.00000000</td>
                            <td>0.01140000</td>
                        </tr>
                        <tr price="0.00000058" amount="20000.00000000">
                            <td>0.00000058</td>
                            <td>20000.00000000</td>
                            <td>0.01140000</td>
                        </tr>
                        <tr price="0.00000059" amount="20000.00000000">
                            <td>0.00000059</td>
                            <td>20000.00000000</td>
                            <td>0.01140000</td>
                        </tr>
                    </tbody>
                </table>
                <hr class="hr-sell">

            </div>
        </div>
    </div>
    @endsection
    @section('scripts')
    <script>
        $("#sell_amount").on("input", function () {
            if ($("#sell_price").val() > 0) {
                $total = $(this).val() * $("#sell_price").val();
                $("#sell_total").text(($total * 0.998).toFixed(8));
                $("#sell_fee").text(($total * 0.002).toFixed(8));
            }
        });
        $("#sell_price").on("input", function () {
            if ($("#sell_amount").val() > 0) {
                $total = $(this).val() * $("#sell_amount").val();
                $("#sell_total").text(($total * 0.998).toFixed(8));
                $("#sell_fee").text(($total * 0.002).toFixed(8));
            }
        });
        $("#buy_amount").on("input", function () {
            if ($("#buy_price").val() > 0) {
                $total = $(this).val() * $("#buy_price").val();
                $("#buy_total").text(($total * 0.998).toFixed(8));
                $("#buy_fee").text(($total * 0.002).toFixed(8));
            }
        });
        $("#buy_price").on("input", function () {
            if ($("#buy_amount").val() > 0) {
                $total = $(this).val() * $("#buy_amount").val();
                $("#buy_total").text(($total * 0.998).toFixed(8));
                $("#buy_fee").text(($total * 0.002).toFixed(8));
            }
        });

        $(".buy_table tr").click(function () {
            $price = $(this).attr("price");
            if ($price == undefined) return;
            $amount = $(this).attr("amount");
            $("#sell_price").val($price);
            $("#sell_amount").val($amount);
            $total = $price * $amount;
            $("#sell_total").text(($total * 0.998).toFixed(8));
            $("#sell_fee").text(($total * 0.002).toFixed(8));
        });
        $(".sell_table tr").click(function () {
            $price = $(this).attr("price");
            if ($price == undefined) return;
            $amount = $(this).attr("amount");
            $("#buy_price").val($price);
            $("#buy_amount").val($amount);
            $total = $price * $amount;
            $("#buy_total").text(($total * 0.998).toFixed(8));
            $("#buy_fee").text(($total * 0.002).toFixed(8));
        });
    </script>
    @endsection
