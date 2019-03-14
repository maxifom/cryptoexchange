<?php

namespace App\Listeners;

use App\Events\TxReceived;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TxReceivedListener
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
     * @param  TxReceived  $event
     * @return void
     */
    public function handle(TxReceived $event)
    {
        //
    }
}
