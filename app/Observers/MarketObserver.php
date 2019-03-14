<?php

namespace App\Observers;

use App\Market;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MarketObserver
{
    /**
     * Handle to the market "created" event.
     *
     * @param  \App\Market $market
     * @return void
     */
    public function created(Market $market)
    {
        $name = DB::select('select name from coins where id = ? LIMIT 1', [$market->base_currency_id])[0]->name . DB::select('select name from coins where id = ? LIMIT 1', [$market->trade_currency_id])[0]->name;
        Cache::forget('Market' . $market->id);
    }

    /**
     * Handle the market "updated" event.
     *
     * @param  \App\Market $market
     * @return void
     */
    public function updated(Market $market)
    {
        $name = DB::select('select name from coins where id = ? LIMIT 1', [$market->base_currency_id])[0]->name . DB::select('select name from coins where id = ? LIMIT 1', [$market->trade_currency_id])[0]->name;
        Cache::forget('Market' . $market->id);
    }

    /**
     * Handle the market "deleted" event.
     *
     * @param  \App\Market $market
     * @return void
     */
    public function deleted(Market $market)
    {
        $name = DB::select('select name from coins where id = ? LIMIT 1', [$market->base_currency_id])[0]->name . DB::select('select name from coins where id = ? LIMIT 1', [$market->trade_currency_id])[0]->name;
        Cache::forget('Market' . $market->id);
    }
}
