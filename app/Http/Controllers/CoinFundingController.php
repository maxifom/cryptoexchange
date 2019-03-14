<?php

namespace App\Http\Controllers;

use App\CoinFunding;
use App\CoinFundingDeposit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoinFundingController extends Controller
{
    public function fundings()
    {
        $fundings = CoinFunding::orderByDesc('id')->get();
        foreach ($fundings as $funding)
        {
            $funding->pending_amount = 0;
            $sum = CoinFundingDeposit::where('funding_id', $funding->id)->where('confirmed',0)->sum('value');
            $funding->pending_amount = $sum;
        }
        return view('fundings')->with(['fundings' => $fundings]);
    }

    public function funding($id)
    {
        $funding = CoinFunding::find($id);
        if ($id == null || $funding == null) {
            return redirect()->back();
        }
        $funding->pending_amount = 0;
        $deposits = CoinFundingDeposit::where('funding_id', $funding->id)->orderByDesc('id')->get();
        if (Auth::user())
            foreach ($deposits as $deposit) {
                $time = Carbon::createFromTimeString($deposit->created_at);
                $time->tz(Auth::user()->timezone);
                $deposit->created_at = $time->format("Y-m-d H:i:s");
            }
        $sum = CoinFundingDeposit::where('funding_id', $funding->id)->where('confirmed',0)->sum('value');
        $funding->pending_amount = $sum;
        return view('funding')->with(['funding' => $funding, 'deposits' => $deposits]);
    }
}
