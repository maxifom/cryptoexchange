<?php

namespace App\Listeners;

use App\Events\TxConfirmation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TxConfirmationListener
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
     * @param  TxConfirmation  $event
     * @return void
     */
    public function handle(TxConfirmation $event)
    {
        //
    }
}
