<?php

namespace App\Observers;

use App\Coin;
use App\Market;
use App\Trade;
use Illuminate\Support\Facades\Cache;

class TradeObserver
{
    /**
     * Handle to the trade "created" event.
     *
     * @param  \App\Trade $trade
     * @return void
     */
    public function created(Trade $trade)
    {
        Cache::forget("AllTradeUser" . $trade->user_id);
        Cache::forget("TradeUser" . $trade->user_id . "doesnt exist");
        if ($trade->type=='sell')
        {
            Cache::forget("TradeMarketSell" . $trade->market_id);
        }
        else if ($trade->type=='buy')
        {
            Cache::forget("TradeMarketBuy" . $trade->market_id);
        }
        $market = Market::where('id',$trade->market_id)->select(['id','base_currency_id','trade_currency_id'])->first();
        $base_coin = Cache::remember('Coin' . $market->base_currency_id, 60, function () use ($market) {
            return Coin::where('id', $market->base_currency_id)->first();
        })->name;
        $trade_coin = Cache::remember('Coin' . $market->trade_currency_id, 60, function () use ($market) {
            return Coin::where('id', $market->trade_currency_id)->first();
        })->name;

        $name = $base_coin . $trade_coin;
        Cache::forget('Market'.$name);
    }

    /**
     * Handle the trade "updated" event.
     *
     * @param  \App\Trade $trade
     * @return void
     */
    public function updated(Trade $trade)
    {
        Cache::forget("AllTradeUser" . $trade->user_id);
        Cache::forget("TradeUser" . $trade->user_id . "doesnt exist");
        if ($trade->type=='sell')
        {
            Cache::forget("TradeMarketSell" . $trade->market_id);

        }
        else if ($trade->type=='buy')
        {
            Cache::forget("TradeMarketBuy" . $trade->market_id);

        }
        $market = Market::where('id',$trade->market_id)->select(['id','base_currency_id','trade_currency_id'])->first();
        $base_coin = Cache::remember('Coin' . $market->base_currency_id, 60, function () use ($market) {
            return Coin::where('id', $market->base_currency_id)->first();
        })->name;
        $trade_coin = Cache::remember('Coin' . $market->trade_currency_id, 60, function () use ($market) {
            return Coin::where('id', $market->trade_currency_id)->first();
        })->name;

        $name = $base_coin . $trade_coin;
        Cache::forget('Market'.$name);

    }


    /**
     * Handle the trade "deleted" event.
     *
     * @param  \App\Trade $trade
     * @return void
     */
    public function deleted(Trade $trade)
    {
        Cache::forget("AllTradeUser" . $trade->user_id);
        Cache::forget("TradeUser" . $trade->user_id . "doesnt exist");
        if ($trade->type=='sell')
        {
            Cache::forget("TradeMarketSell" . $trade->market_id);

        }
        else if ($trade->type=='buy')
        {
            Cache::forget("TradeMarketBuy" . $trade->market_id);

        }
        $market = Market::where('id',$trade->market_id)->select(['id','base_currency_id','trade_currency_id'])->first();
        $base_coin = Cache::remember('Coin' . $market->base_currency_id, 60, function () use ($market) {
            return Coin::where('id', $market->base_currency_id)->first();
        })->name;
        $trade_coin = Cache::remember('Coin' . $market->trade_currency_id, 60, function () use ($market) {
            return Coin::where('id', $market->trade_currency_id)->first();
        })->name;

        $name = $base_coin . $trade_coin;
        Cache::forget('Market'.$name);
    }
}
