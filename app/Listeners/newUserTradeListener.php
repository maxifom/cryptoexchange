<?php

namespace App\Listeners;

use App\Events\newUserTrade;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class newUserTradeListener
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
     * @param  newUserTrade  $event
     * @return void
     */
    public function handle(newUserTrade $event)
    {
        //
    }
}
