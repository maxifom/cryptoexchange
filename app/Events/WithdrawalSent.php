<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Wallet;
use Illuminate\Support\Facades\Cache;
use TrayLabs\InfluxDB\Facades\InfluxDB;
use InfluxDB\Point;
use InfluxDB\Database\Exception;
use InfluxDB\Database;
class WithdrawalSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public function __construct(\App\Withdrawal $withdrawal,$tx)
    {
        $w1=$withdrawal;
        if ($w1->tx == NULL)
        {
            $w1->tx=$tx;
        }
        $w1->name = $w1->wallet->coin->name;
        if (gettype($w1)=='object')
        {
            $w1 = $w1->formatDates();
        }
        $this->data=["withdrawal"=>$w1];
        $user_id = Wallet::find($withdrawal->wallet_id)->user_id;
        $text = "User " . $user_id . " withrawal sent for " . $w1->value . " " . $w1->name;
        list($usec, $sec) = explode(' ', microtime());
        $timestamp = sprintf('%d%06d', $sec, $usec*1000000);
        try {
            $points = array(new Point
            (
                'live_update',
                null,
                ['type' => 'withdrawal'],
                ['text' => $text],
                $timestamp
            ));
            InfluxDB::writePoints($points,Database::PRECISION_MICROSECONDS);
        } catch (Exception $e) {

        }
    }
    public function broadcastAs()
    {
        return "WithdrawalSent";
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
        return array(new PrivateChannel('balances.'.$user_id));
    }
}
