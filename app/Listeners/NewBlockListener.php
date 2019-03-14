<?php

namespace App\Listeners;

use App\Events\NewBlock;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewBlockListener
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
     * @param  NewBlock  $event
     * @return void
     */
    public function handle(NewBlock $event)
    {
        //
    }
    
}
