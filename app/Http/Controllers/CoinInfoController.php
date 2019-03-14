<?php

namespace App\Http\Controllers;
use App\CoinInfo;
use Illuminate\Support\Facades\Cache;
use App\Coin;
class CoinInfoController extends Controller
{
    public function coinInfos()
    {
        $coinInfos = Cache::remember('CoinInfos',60,function(){
            return CoinInfo::all()->sortBy('coin_id');
        });
        foreach ($coinInfos as $coinInfo)
        {
            $coin = Cache::remember("Coin".$coinInfo->coin_id,60,function() use($coinInfo)
            {
               return Coin::where('id',$coinInfo->coin_id)->first();
            });
            $coinInfo->name = $coin->name;
        }
        return view('coinInfos')->with(["coinInfos" => $coinInfos->toArray()]);
    }
}
