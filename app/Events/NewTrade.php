<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Market;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
class NewTrade implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public function __construct(\App\Trade $trade)
    {
        $market = Cache::remember("Market".$trade->market_id,60,function() use($trade)
        {
            return Market::where('id',$trade->market_id)->first();
        });
        $trade->market = $market->base_coin->name."/".$market->trade_coin->name;
        if (gettype($trade)=='object')
        {
            $trade = $trade->formatDates();
        }
        $trade->amount_traded = 0;
        $this->data=["trade"=>$trade];

    }
    public function broadcastAs()
    {
        return "NewTrade";
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $user_id=$this->data['trade']->user_id;
        $market_id = $this->data['trade']->market_id;
        return array(new PrivateChannel('trades.'.$user_id),new Channel('market.'.$market_id));
    }
}
