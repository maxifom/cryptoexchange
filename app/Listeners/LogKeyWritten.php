<?php

namespace App\Listeners;

use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
class LogKeyWritten implements ShouldQueue
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
     * @param  KeyWritten  $event
     * @return void
     */
    public function handle(KeyWritten $event)
    {
        Storage::append("cache.txt","Key written:".$event->key."-".$event->value." for ".$event->minutes." minutes");
    }
}
