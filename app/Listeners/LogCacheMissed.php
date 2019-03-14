<?php

namespace App\Listeners;

use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
class LogCacheMissed implements ShouldQueue
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
     * @param  CacheMissed  $event
     * @return void
     */
    public function handle(CacheMissed $event)
    {
        Storage::append("cache.txt","Cache missed:".$event->key);
    }
}
