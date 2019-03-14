<?php

namespace App\Observers;

use App\Withdrawal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class WithdrawalObserver
{
    /**
     * Handle to the withdrawal "created" event.
     *
     * @param  \App\Withdrawal  $withdrawal
     * @return void
     */
    public function created(Withdrawal $withdrawal)
    {
        $user_id=DB::select('select user_id from wallets where id = ? LIMIT 1',[$withdrawal->wallet_id])[0]->user_id;
        Cache::forget('Withdrawal'.$user_id);
        Cache::forget('AllWithdrawal'.$user_id);
    }

    /**
     * Handle the withdrawal "updated" event.
     *
     * @param  \App\Withdrawal  $withdrawal
     * @return void
     */
    public function updated(Withdrawal $withdrawal)
    {
        $user_id=DB::select('select user_id from wallets where id = ? LIMIT 1',[$withdrawal->wallet_id])[0]->user_id;
        Cache::forget('Withdrawal'.$user_id);
        Cache::forget('AllWithdrawal'.$user_id);
    }

    /**
     * Handle the withdrawal "deleted" event.
     *
     * @param  \App\Withdrawal  $withdrawal
     * @return void
     */
    public function deleted(Withdrawal $withdrawal)
    {
        $user_id=DB::select('select user_id from wallets where id = ? LIMIT 1',[$withdrawal->wallet_id])[0]->user_id;
        Cache::forget('Withdrawal'.$user_id);
        Cache::forget('AllWithdrawal'.$user_id);
    }
}
