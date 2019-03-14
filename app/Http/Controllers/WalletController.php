<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coin;
use App\Rules\isNumeric;
use App\Rules\NumericLessThan;
use App\Rules\NumericMoreThan;
use App\Rules\ValidAddress;
use App\Wallet;
use Illuminate\Support\Facades\Cache;
use App\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Jobs\WithdrawalConfirmationJob;
use Denpa\Bitcoin\Client as BitcoinClient;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function createNewAddress(Request $request)
    {
        $request->validate([
           'name'=>'required'
        ]);
        return DB::transaction(function () use ($request) {

            $coin_name = $request->post('name');
            $user = Auth::user();
            $coin = Cache::remember("Coin" . $coin_name, 60, function () use ($coin_name) {
                return Coin::where('name', $coin_name)->first();
            });
            if ($coin->status!='confirmed' && $user->admin===0)
            {
                return -1;
            }
            $walletFind = Cache::remember("WalletUser" . $user->id . "Coin" . $coin->id, 60, function () use ($coin, $user) {
                return Wallet::where('coin_id', $coin->id)->where('user_id', $user->id)->first();
            });
            if ($walletFind != NULL) {
                if ($walletFind->address != NULL) {
                    return -1;
                }
                $bitcoind = new BitcoinClient('http://' . $coin['user'] . ':' . $coin['pass'] . '@localhost:' . $coin['port'] . '/');
                $walletFind->address = $bitcoind->getnewaddress()->get();
                $walletFind->save();
                return json_encode(["address" => $walletFind->address]);
            } else {
                return -1;
            }
        },1);

    }

    public function withdraw(Request $request)
    {
        $request->validate([
           'name'=>'required'
        ]);
        return DB::transaction(function () use ($request) {

            $user = Auth::user();
            $coin_name = $request->post('name');
            $coin = Cache::remember("Coin" . $coin_name, 60, function () use ($coin_name) {
                return Coin::where('name', $coin_name)->select('id')->first();
            });
            if ($coin->status!='confirmed' && $user->admin===0)
            {
                return -1;
            }
            $fee = Cache::remember('Fee' . $coin->id, 60, function () use ($coin) {
                return Fee::where('coin_id', $coin->id)->first();
            });
            $wallet = Wallet::where('coin_id', $coin->id)->where('user_id', $user->id)->lockForUpdate()->first();
            $value = $request->post('value');
            if (is_numeric($value))
                $value = number_format($value, 8, '.', '');
            $address =$request->post('address');
            $validator = Validator::make(["value" => $value, "address" => $address], [
                'value' => ["bail", "required", new isNumeric(), new NumericLessThan($fee->fee * 1.5), new NumericMoreThan($wallet->balance)],
                'address' => ["bail", "required", new ValidAddress($coin_name)]
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput($request->except(['_token', 'name']))->withErrors($validator);
            }
            $w = Withdrawal::create(["wallet_id" => $wallet->id, 'value' => number_format($value - $fee->fee, 8, '.', ''), 'address' => $address, 'status' => 'requested']);
            $wallet->balance -= floatval(number_format($value, 8, '.', ''));
            $wallet->save();
            WithdrawalConfirmationJob::dispatch($w->id,$user->name);
            return redirect()->route('withdrawalConfirmView');
        },1);
    }

}
