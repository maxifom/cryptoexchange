<?php

namespace App\Listeners;

use App\Events\TradeSuccessful;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TradeSuccessfulListener
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
     * @param  TradeSuccessful  $event
     * @return void
     */
    public function handle(TradeSuccessful $event)
    {
        //
    }
}
