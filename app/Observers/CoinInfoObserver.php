<?php

namespace App\Observers;

use App\CoinInfo;
use Illuminate\Support\Facades\Cache;
class CoinInfoObserver
{
    /**
     * Handle to the fee "created" event.
     *
     * @param  \App\CoinInfo  $coinInfo
     * @return void
     */
    public function created(CoinInfo $coinInfo)
    {
        Cache::forget('CoinInfo'.$coinInfo->coin_id);
        Cache::forget('CoinInfos');
    }

    /**
     * Handle the fee "updated" event.
     *
     * @param  \App\CoinInfo  $coinInfo
     * @return void
     */
    public function updated(CoinInfo $coinInfo)
    {
        Cache::forget('CoinInfo'.$coinInfo->coin_id);
        Cache::forget('CoinInfos');
    }

    /**
     * Handle the fee "deleted" event.
     *
     * @param  \App\CoinInfo  $coinInfo
     * @return void
     */
    public function deleted(CoinInfo $coinInfo)
    {
        Cache::forget('CoinInfo'.$coinInfo->coin_id);
        Cache::forget('CoinInfos');
    }
}
