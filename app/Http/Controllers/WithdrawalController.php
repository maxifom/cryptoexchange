<?php

namespace App\Http\Controllers;

use App\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Wallet;
use App\Coin;
use Illuminate\Support\Facades\Cache;
use App\Fee;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function withdraw($coin_name)
    {
        $coin = Cache::remember('Coin' . $coin_name, 60, function () use ($coin_name) {
            return Coin::where('name', $coin_name)->first();
        });
        $user = Auth::user();
        $wallet = Cache::remember("WalletUser" . $user->id . "Coin" . $coin->id, 60, function () use ($coin, $user) {
            return Wallet::where('coin_id', $coin->id)->where('user_id', $user->id)->first();
        });
        $fee = Cache::remember('Fee' . $coin->id, 60, function () use ($coin) {
            return Fee::where('coin_id', $coin->id)->first();
        });
        $fee_value = $fee->fee;
        return view('withdraw')->with(["coin" => $coin, "wallet" => $wallet, 'fee' => $fee_value]);
    }

    public function cancel(Request $request)
    {
        $request->validate([
           'id'=>'required|integer'
        ]);
        $w_id = $request->post('id');
        $w = Withdrawal::find($w_id);
        if ($w && $w->status == 'requested') {
            return DB::transaction(function () use ($w) {
                $value = $w->value;
                $wallet = Wallet::find($w->wallet_id);
                if ($wallet->user_id!=Auth::id())
                {
                    return 0;
                }
                $coin = Coin::find($wallet->coin_id);
                $fee = $coin->fee->fee;
                $value += $fee;
                $wallet->balance += $value;
                $w->delete();
                $wallet->save();
                return 1;
            },1);
        } else {
            return 0;
        }
    }
}
