<?php

namespace App\Observers;
use Illuminate\Support\Facades\Cache;
use App\Wallet;
class WalletObserver
{
    /**
     * Handle to the wallet "created" event.
     *
     * @param  \App\Wallet  $wallet
     * @return void
     */
    public function created(Wallet $wallet)
    {
        Cache::forget('WalletUser'.$wallet->user_id);
        Cache::forget('WalletCoin'.$wallet->coin_id);
        Cache::forget('Wallet'.$wallet->id);
        Cache::forget('WalletUser'.$wallet->user_id.'Coin'.$wallet->coin_id);
    }

    /**
     * Handle the wallet "updated" event.
     *
     * @param  \App\Wallet  $wallet
     * @return void
     */
    public function updated(Wallet $wallet)
    {
        Cache::forget('WalletUser'.$wallet->user_id);
        Cache::forget('WalletCoin'.$wallet->coin_id);
        Cache::forget('Wallet'.$wallet->id);
        Cache::forget('WalletUser'.$wallet->user_id.'Coin'.$wallet->coin_id);


    }

    /**
     * Handle the wallet "deleted" event.
     *
     * @param  \App\Wallet  $wallet
     * @return void
     */
    public function deleted(Wallet $wallet)
    {
        Cache::forget('WalletUser'.$wallet->user_id);
        Cache::forget('WalletCoin'.$wallet->coin_id);
        Cache::forget('Wallet'.$wallet->id);
        Cache::forget('WalletUser'.$wallet->user_id.'Coin'.$wallet->coin_id);
    }
}
