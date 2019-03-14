<?php

namespace App\Listeners;

use App\Events\TxConfirmed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TxConfirmedListener
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
     * @param  TxConfirmed  $event
     * @return void
     */
    public function handle(TxConfirmed $event)
    {
        //
    }
}
