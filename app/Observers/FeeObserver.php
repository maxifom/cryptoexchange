<?php

namespace App\Observers;

use App\Fee;
use Illuminate\Support\Facades\Cache;
class FeeObserver
{
    /**
     * Handle to the fee "created" event.
     *
     * @param  \App\Fee  $fee
     * @return void
     */
    public function created(Fee $fee)
    {
        Cache::forget('Fee'.$fee->coin_id);
    }

    /**
     * Handle the fee "updated" event.
     *
     * @param  \App\Fee  $fee
     * @return void
     */
    public function updated(Fee $fee)
    {
        Cache::forget('Fee'.$fee->coin_id);
    }

    /**
     * Handle the fee "deleted" event.
     *
     * @param  \App\Fee  $fee
     * @return void
     */
    public function deleted(Fee $fee)
    {
        Cache::forget('Fee'.$fee->coin_id);
    }
}
