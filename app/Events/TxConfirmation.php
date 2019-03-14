<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Wallet;
use Illuminate\Support\Facades\Cache;
class TxConfirmation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public function __construct(\App\Deposit $d)
    {
        $d1 = $d;
        $c = $d1->wallet->coin;
        $d1->name = $c->name;
        $d1->needed_confirmations = $c->needed_confirmations;
        if (gettype($d1)=='object')
        {
            $d1 = $d1->formatDates();
        }
        $this->data=["deposit"=>$d1];
    }
    public function broadcastAs()
    {
        return "TxConfirmation";
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $user_id = Cache::remember("Wallet".$this->data['deposit']->wallet_id,60,function(){
            return Wallet::where('id',$this->data['deposit']->wallet_id)->first();
        })->user_id;
        return new PrivateChannel('balances.'.$user_id);
    }
}
