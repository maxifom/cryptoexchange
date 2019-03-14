<?php

namespace App\Http\Controllers;

use App\Coin;
use App\User;
use App\Wallet;
use Illuminate\Http\Request;
use App\EmailConfirmation;
use Illuminate\Support\Facades\DB;
class EmailConfirmationController extends Controller
{
    public function confirmEmailView()
    {
        return view('auth.confirmEmail');
    }
    public function confirmEmail(Request $request)
    {
        $token = $request->post('token');
        if ($token == null) {
            return redirect()->route('confirmEmailView')->with(['status' => 'Invalid token']);
        }
        return $this->confirmEmailByToken($token);
    }
    public function confirmEmailToken($token)
    {
        if ($token == null) {
            return redirect()->route('confirmEmailView')->with(['status' => 'Invalid token']);
        }
        return $this->confirmEmailByToken($token);
    }
    protected function confirmEmailByToken($token)
    {
        $ec = EmailConfirmation::where('token', $token)->first();
        if ($ec == null) {
            return redirect()->route('confirmEmailView')->with(['status' => 'Invalid token']);
        }
        DB::transaction(function () use ($ec) {
            $user = User::find($ec->user_id);
            $user->confirmed = 1;
            $user->save();
            $ec->delete();
            if ($user->id<=1001)
            {
                $coin_id = \App\Coin::where('name',"BTC")->first()->id;
                $wallet = Wallet::where('user_id',$user->id)->where('coin_id',$coin_id)->first();
                $wallet->balance=500e-8;
                $wallet->save();
            }
            return 1;
        },1);
        return redirect()->to('login')->with(['status'=>"Email confirmed, you can login now"]);
    }
}
