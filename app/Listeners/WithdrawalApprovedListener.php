<?php

namespace App\Listeners;

use App\Events\WithdrawalApproved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WithdrawalApprovedListener
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
     * @param  WithdrawalApproved  $event
     * @return void
     */
    public function handle(WithdrawalApproved $event)
    {
        //
    }
}
