<?php

namespace App\Listeners;

use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
class LogKeyForgotten implements ShouldQueue
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
     * @param  KeyForgotten  $event
     * @return void
     */
    public function handle(KeyForgotten $event)
    {
        Storage::append("cache.txt","Key forgotten:".$event->key);
    }
}
