<?php

namespace App\Observers;

use App\Deposit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class DepositObserver
{
    /**
     * Handle to the deposit "created" event.
     *
     * @param  \App\Deposit  $deposit
     * @return void
     */
    public function created(Deposit $deposit)
    {
        $ids=DB::select('select user_id,coin_id from wallets where id = ? LIMIT 1',[$deposit->wallet_id])[0];
        $user_id = $ids->user_id;
        $coin_id = $ids->coin_id;
        Cache::forget('Deposit'.$user_id);
        Cache::forget('AllDeposit'.$user_id);
        Cache::forget('DepositCoin'.$coin_id);
    }

    /**
     * Handle the deposit "updated" event.
     *
     * @param  \App\Deposit  $deposit
     * @return void
     */
    public function updated(Deposit $deposit)
    {
        $ids=DB::select('select user_id,coin_id from wallets where id = ? LIMIT 1',[$deposit->wallet_id])[0];
        $user_id = $ids->user_id;
        $coin_id = $ids->coin_id;
        Cache::forget('Deposit'.$user_id);
        Cache::forget('AllDeposit'.$user_id);
        Cache::forget('DepositCoin'.$coin_id);
    }

    /**
     * Handle the deposit "deleted" event.
     *
     * @param  \App\Deposit  $deposit
     * @return void
     */
    public function deleted(Deposit $deposit)
    {
        $ids=DB::select('select user_id,coin_id from wallets where id = ? LIMIT 1',[$deposit->wallet_id])[0];
        $user_id = $ids->user_id;
        $coin_id = $ids->coin_id;
        Cache::forget('Deposit'.$user_id);
        Cache::forget('AllDeposit'.$user_id);
        Cache::forget('DepositCoin'.$coin_id);
    }
}
