<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\IpConfirmation;
use App\Jobs\ipConfirmation as ipJob;
use App\UserIp;
use Illuminate\Support\Facades\Auth;
class IpConfirmationController extends Controller
{
    public function ipConfirmView()
    {
        return view('auth.confirmIp');
    }
    public function confirmIp(Request $request)
    {
        $token = $request->post('token');
        if ($token==null)
            return redirect()->back()->with(['status'=>"Invalid token"]);
        return $this->confirmIpByToken($token);
    }
    public function confirmIpToken($token)
    {
        if ($token==null)
            return redirect()->back()->with(['status'=>"Invalid token"]);
        return $this->confirmIpByToken($token);
    }
    protected function confirmIpByToken($token)
    {
        $ip_confirmation = IpConfirmation::where("token", $token)->first();
        if ($ip_confirmation == null) {
            return redirect('ipConfirmView')->with(['status' => 'Invalid token']);
        } else {
            $date = $ip_confirmation->created_at;
            $ip = $ip_confirmation->ip;
            $user_id = $ip_confirmation->user_id;
            if ($date->diffInMinutes(\Carbon\Carbon::now()) > 60) {
                $result = DB::transaction(function () use ($ip_confirmation) {
                    $ip_confirmation->delete();
                    return 1;
                },1);
                if ($result == 1) {
                    ipJob::dispatch($ip, $user_id);
                    return redirect()->route('ipConfirmView')->with(['status' => "Check your email for confirmation"]);
                }
            } else {
                if (DB::transaction(function () use ($user_id, $ip) {
                    UserIp::create(['user_id' => $user_id, 'ip' => $ip]);
                    return 1;
                },1)) {
                    DB::transaction(function () use ($ip_confirmation,$user_id) {
                        $ip_confirmation->delete();
                    },1);
                }
            }
        }
        if (Auth::user()==null)
        {
            return redirect('login')->with(['status'=>"IP successfully confirmed, you may now login."]);
        }
        return redirect('wallets')->with(['status'=>"IP successfully confirmed."]);
    }
}
