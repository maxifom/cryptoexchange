<?php

namespace App\Observers;

use App\UserTrade;
use Illuminate\Support\Facades\Cache;
class UserTradeObserver
{
    /**
     * Handle to the user trade "created" event.
     *
     * @param  \App\UserTrade  $userTrade
     * @return void
     */
    public function created(UserTrade $userTrade)
    {
        Cache::forget('UserTradeUser'.$userTrade->user_id_taker);
        Cache::forget('UserTradeUser'.$userTrade->user_id_maker);
        Cache::forget('UserTradeMarket'.$userTrade->market_id);
    }

    /**
     * Handle the user trade "updated" event.
     *
     * @param  \App\UserTrade  $userTrade
     * @return void
     */
    public function updated(UserTrade $userTrade)
    {
        Cache::forget('UserTradeUser'.$userTrade->user_id_taker);
        Cache::forget('AllUserTradeUser'.$userTrade->user_id_taker);
        Cache::forget('AllUserTradeUser'.$userTrade->user_id_maker);
        Cache::forget('UserTradeUser'.$userTrade->user_id_maker);
        Cache::forget('UserTradeMarket'.$userTrade->market_id);
    }

    /**
     * Handle the user trade "deleted" event.
     *
     * @param  \App\UserTrade  $userTrade
     * @return void
     */
    public function deleted(UserTrade $userTrade)
    {
        Cache::forget('UserTradeUser'.$userTrade->user_id_taker);
        Cache::forget('AllUserTradeUser'.$userTrade->user_id_taker);
        Cache::forget('AllUserTradeUser'.$userTrade->user_id_maker);
        Cache::forget('UserTradeUser'.$userTrade->user_id_maker);
        Cache::forget('UserTradeMarket'.$userTrade->market_id);
    }
}
