<?php

namespace App\Listeners;

use App\Events\TradeUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TradeUpdatedListener
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
     * @param  TradeUpdated  $event
     * @return void
     */
    public function handle(TradeUpdated $event)
    {
        //
    }
}
