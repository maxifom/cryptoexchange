<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MarketHistory;
class MarketHistoryController extends Controller
{
    public function getMarketHistory($market_id){
        $mh = MarketHistory::where('market_id',$market_id)->limit(48)->get()->toJson();
        return $mh;
    }
}
