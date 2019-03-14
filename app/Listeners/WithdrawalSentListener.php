<?php

namespace App\Listeners;

use App\Events\WithdrawalSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WithdrawalSentListener
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
     * @param  WithdrawalSent  $event
     * @return void
     */
    public function handle(WithdrawalSent $event)
    {
        //
    }
}
