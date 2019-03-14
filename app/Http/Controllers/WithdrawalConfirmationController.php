<?php

namespace App\Http\Controllers;

use App\Coin;
use App\Withdrawal;
use Illuminate\Http\Request;
use App\WithdrawalConfirmation;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessWithdrawal;
use App\Wallet;
class WithdrawalConfirmationController extends Controller
{
    public function withdrawalConfirmView()
    {
        return view('withdrawalConfirm');
    }

    public function withdrawalConfirm(Request $request)
    {
        $token = $request->post('token');
        if ($token == null) {
            return redirect()->route('withdrawalConfirmView')->with(['status' => "Invalid token"]);
        }
        return $this->withdrawalConfirmByToken($token);
    }

    protected
    function withdrawalConfirmByToken($token)
    {
        $wc = WithdrawalConfirmation::where('token', $token)->first();
        if ($wc == null) {
            return redirect()->route('withdrawalConfirmView')->with(['status' => "Invalid token"]);
        }
        $r = DB::transaction(function () use ($wc) {
            $withdrawal = Withdrawal::where('id', $wc->withdrawal_id)->first();
            $withdrawal->status = 'approved';
            $withdrawal->save();
            $wc->delete();
            return array('result' => 1, 'withdrawal_id' => $withdrawal->id);
        },1);
        if ($r['result'] == 1) {
            ProcessWithdrawal::dispatch($r['withdrawal_id']);
            return redirect()->route('wallets')->with(['status' => 'Withdrawal confirmed']);
        }

    }

    public function withdrawalConfirmToken($token)
    {
        if ($token == null) {
            return redirect()->route('withdrawalConfirmView')->with(['status' => "Invalid token"]);
        }
        return $this->withdrawalConfirmByToken($token);
    }

    public function rejectWithdrawaltoken($token)
    {
        if ($token == null) {
            return redirect()->route('withdrawalConfirmView')->with(['status' => "Invalid token"]);
        }
        return DB::transaction(function () use($token) {
            $wc = WithdrawalConfirmation::where('token', $token)->first();
            if (!$wc)
            {
                return redirect()->route('wallets');
            }
            $w = Withdrawal::find($wc->withdrawal_id);
            if ($w->status!='requested')
            {
                return redirect()->route('wallets');
            }
            $value = $w->value;
            $wallet = Wallet::where('id',$w->wallet_id)->lockForUpdate()->first();
            $coin = Coin::find($wallet->coin_id);
            $fee = $coin->fee->fee;
            $value += $fee;
            $wallet->balance += $value;
            $w->delete();
            $wc->delete();
            $wallet->save();
            return redirect('wallets')->with(['status' => 'Withdrawal rejected']);
        }, 1);
    }
}
