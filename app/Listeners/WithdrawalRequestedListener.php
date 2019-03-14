<?php

namespace App\Listeners;

use App\Events\WithdrawalRequested;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WithdrawalRequestedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WithdrawalRequested  $event
     * @return void
     */
    public function handle(WithdrawalRequested $event)
    {
        //
    }
}
