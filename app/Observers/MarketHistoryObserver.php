<?php

namespace App\Observers;

use App\MarketHistory;
use Illuminate\Support\Facades\Cache;
class MarketHistoryObserver
{
    /**
     * Handle to the market history "created" event.
     *
     * @param  \App\MarketHistory  $marketHistory
     * @return void
     */
    public function created(MarketHistory $marketHistory)
    {
        Cache::forget('MarketHistory'.$marketHistory->market_id);
    }

    /**
     * Handle the market history "updated" event.
     *
     * @param  \App\MarketHistory  $marketHistory
     * @return void
     */
    public function updated(MarketHistory $marketHistory)
    {
        Cache::forget('MarketHistory'.$marketHistory->market_id);
    }

    /**
     * Handle the market history "deleted" event.
     *
     * @param  \App\MarketHistory  $marketHistory
     * @return void
     */
    public function deleted(MarketHistory $marketHistory)
    {
        Cache::forget('MarketHistory'.$marketHistory->market_id);
    }
}
