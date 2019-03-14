<?php

namespace App\Events;

use App\UserTrade;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Market;
use Illuminate\Support\Facades\Cache;
class TradeSuccessful implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    private $trade;
    public function __construct(\App\Trade $trade,$amount_traded)
    {
        $this->trade = $trade;
        $market = Cache::remember("Market".$trade->market_id,60,function() use($trade)
        {
            return Market::where('id',$trade->market_id)->first();
        });
        $trade->market = $market->base_coin->name."/".$market->trade_coin->name;
        $trade->amount_traded = $amount_traded;
        if (gettype($trade)=='object')
        {
            $trade = $trade->formatDates();
        }
        $trade->amount_trade = UserTrade::where('trade_id',$trade->id)->sum('amount');
        $this->data=["trade"=>$trade];
    }
    public function broadcastAs()
    {
        return "TradeSuccessful";
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $user_id=$this->trade->user_id;
        $market_id = $this->trade->market_id;
        return array(new PrivateChannel('trades.'.$user_id),new Channel('market.'.$market_id));
    }
}
