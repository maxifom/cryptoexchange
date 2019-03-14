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
class WithdrawalRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public function __construct(\App\Withdrawal $withdrawal)
    {
        $w1 = $withdrawal;
        $w1->name = $w1->wallet->coin->name;
        if (gettype($w1)=='object')
        {
            $w1 = $w1->formatDates();
        }
        $this->data=["withdrawal"=>$w1];
    }
    public function broadcastAs()
    {
        return "WithdrawalRequested";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $user_id = Cache::remember("Wallet".$this->data['withdrawal']->wallet_id,60,function(){
            return Wallet::where('id',$this->data['withdrawal']->wallet_id)->first();
        })->user_id;
        return new PrivateChannel('balances.'.$user_id);
    }
}
