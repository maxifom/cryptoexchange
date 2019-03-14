<?php

namespace App\Listeners;

use App\Events\TradeDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TradeDeletedListener
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
     * @param  TradeDeleted  $event
     * @return void
     */
    public function handle(TradeDeleted $event)
    {
        //
    }
}
