<?php

namespace App\Observers;

use App\Coin;
use Illuminate\Support\Facades\Cache;
class CoinObserver
{
    /**
     * Handle to the coin "created" event.
     *
     * @param  \App\Coin  $coin
     * @return void
     */
    public function created(Coin $coin)
    {
        Cache::forget('Coin'.$coin->name);
        Cache::forget('Coin'.$coin->id);
        Cache::forget('Coins');
    }

    /**
     * Handle the coin "updated" event.
     *
     * @param  \App\Coin  $coin
     * @return void
     */
    public function updated(Coin $coin)
    {
        Cache::forget('Coin'.$coin->name);
        Cache::forget('Coin'.$coin->id);
        Cache::forget('Coins');
    }

    /**
     * Handle the coin "deleted" event.
     *
     * @param  \App\Coin  $coin
     * @return void
     */
    public function deleted(Coin $coin)
    {
        Cache::forget('Coin'.$coin->name);
        Cache::forget('Coin'.$coin->id);
        Cache::forget('Coins');
    }
}
