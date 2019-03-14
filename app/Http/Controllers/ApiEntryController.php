<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\ApiEntry;
use App\ApiIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ApiEntryController extends Controller
{
    public function createView(Request $request)
    {
        return view('api.create')->with(['ip' => $request->ip()]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'ip1' => 'required|ipv4'
        ]);
        if (ApiEntry::where('user_id', Auth::id())->count() >= 5) {
            return redirect()->route('api_tokens')->with(['status' => 'Maximum 5 tokens allowed']);
        }
        return DB::transaction(function () use ($request) {
            $type = $request->post('type');
            $token = str_replace('-', '', Uuid::uuid4());
            if ($type == 0) {
                $a = ApiEntry::create([
                    'user_id' => Auth::id(),
                    'token' => $token
                ]);
            } else if ($type == 1) {
                $a = ApiEntry::create([
                    'user_id' => Auth::id(),
                    'token' => $token,
                    'wallet' => 1
                ]);
            } else if ($type == 2) {
                $a = ApiEntry::create([
                    'user_id' => Auth::id(),
                    'token' => $token,
                    'trade' => 1
                ]);
            } else if ($type == 3) {
                $a = ApiEntry::create([
                    'user_id' => Auth::id(),
                    'token' => $token,
                    'wallet' => 1,
                    'trade' => 1
                ]);
            } else return redirect()->route('api_tokens')->with(['status' => 'Failed creating token']);
            $ip1 = $request->post('ip1');
            $ip2 = $request->post('ip2');
            $ip3 = $request->post('ip3');
            $ip4 = $request->post('ip4');
            $ip5 = $request->post('ip5');
            $_ip1 = ip2long($ip1);
            ApiIp::firstOrCreate([
                'api_id' => $a->id,
                'api_ip' => $_ip1
            ]);
            if ($ip2 != null) {
                $_ip2 = ip2long($ip2);
                ApiIp::firstOrCreate([
                    'api_id' => $a->id,
                    'api_ip' => $_ip2
                ]);
            }
            if ($ip3 != null) {
                $_ip3 = ip2long($ip3);
                ApiIp::firstOrCreate([
                    'api_id' => $a->id,
                    'api_ip' => $_ip3
                ]);
            }
            if ($ip4 != null) {
                $_ip4 = ip2long($ip4);
                ApiIp::firstOrCreate([
                    'api_id' => $a->id,
                    'api_ip' => $_ip4
                ]);
            }
            if ($ip5 != null) {
                $_ip5 = ip2long($ip5);
                ApiIp::firstOrCreate([
                    'api_id' => $a->id,
                    'api_ip' => $_ip5
                ]);
            }
            return redirect()->route('api_tokens')->with(['status' => 'Successfully created token']);
        }, 1);
    }

    public function tokens()
    {
        $tokens = ApiEntry::where('user_id', Auth::id())->get();
        return view('api.tokens')->with(['tokens' => $tokens]);
    }

    public function docs()
    {
        return view('api.docs');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'token_token' => 'required|min:32|max:32'
        ]);
        $token = $request->post('token_token');
        return DB::transaction(function () use ($token) {
            $api = ApiEntry::where('token',$token)->first();
            if ($api->user_id === Auth::id())
            {
                $api->delete();
                return redirect()->route('api_tokens')->with(['status'=>'Token deleted']);
            }
            else
            {
                return redirect()->back();
            }
        }, 1);
    }
}
