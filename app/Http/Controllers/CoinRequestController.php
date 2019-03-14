<?php

namespace App\Http\Controllers;

use App\CoinRequest;
use App\Jobs\RequestCreatedJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoinRequestController extends Controller
{
    public function requestForm()
    {
        return view('dev.request');
    }

    public function requests()
    {
        $requests = CoinRequest::where('user_id', Auth::id())->orderByDesc('id')->get();
        $count = CoinRequest::where('user_id',Auth::id())->where('status','created')->count();
        return view('dev.requests')->with(['requests' => $requests,'count'=>$count]);
    }

    public function request(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'source' => "required",
            //"block_explorer"=>'required',
            //'announcement' => 'required',
            'type' => 'required',
            'needed_confirmations' => 'required|integer'
        ]);
        if (CoinRequest::where('user_id',Auth::id())->where('status','created')->count()>0)
        {
            return redirect()->route('dev_requests')->with(['status'=>'Max 1 created request']);
        }
        return DB::transaction(function () use ($request) {
            $name = strip_tags($request->post('name'));
            $source = strip_tags($request->post('source'));
            $block_explorer = strip_tags($request->post('block_explorer'));
            $announcement = strip_tags($request->post('announcement'));
            $type = strip_tags($request->post('type'));
            $needed_confirmations = strip_tags($request->post('needed_confirmations'));
            $cr=CoinRequest::create([
                'user_id' => Auth::id(),
                'source' => $source,
                'block_explorer' => $block_explorer,
                'name' => $name,
                'announcement' => $announcement,
                'type' => $type,
                'needed_confirmations' => $needed_confirmations,
            ]);
            RequestCreatedJob::dispatch($cr);
            return redirect()->route('dev_requests')->with(['status' => 'Successfully created request']);
        }, 1);

    }

    public function deleteRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer'
        ]);
        $request_id = $request->post('request_id');
        $_request = CoinRequest::find($request_id);
        if ($_request && $_request->user_id === Auth::id() && $_request->status=='created') {
            return DB::transaction(function () use ($_request) {
                $_request->delete();
                return redirect()->route('dev_requests')->with(['status' => 'Successfully deleted request']);
            }, 1);
        } else {
            return redirect()->back();
        }
    }
}
