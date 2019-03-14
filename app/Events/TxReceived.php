<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Cache;
use App\Wallet;
use TrayLabs\InfluxDB\Facades\InfluxDB;
use InfluxDB\Point;
use InfluxDB\Database\Exception;
use InfluxDB\Database;
class TxReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    private $deposit;
    public function __construct(\App\Deposit $d)
    {
        $d1 = $d;
        $c = $d1->wallet->coin;
        $d1->name = $c->name;
        $d1->needed_confirmations = $c->needed_confirmations;
        if (!isset($d1->confirmations))
        {
            $d1->confirmations = 0;
        }
        if (gettype($d1)=='object')
        {
            $d1 = $d1->formatDates();
        }
        $this->data=["deposit"=>$d1];
        $user_id = $d1->wallet->user_id;
        $text = "User " . $user_id . " deposited " . $d1->value . " " . $d1->name;
        list($usec, $sec) = explode(' ', microtime());
        $timestamp = sprintf('%d%06d', $sec, $usec*1000000);
        try {
            $points = array(new Point
            (
                'live_update',
                null,
                ['type' => 'deposit'],
                ['text' => $text],
                $timestamp
            ));
            InfluxDB::writePoints($points,Database::PRECISION_MICROSECONDS);
        } catch (Exception $e) {

        }
    }
    public function broadcastAs()
    {
        return "TxReceived";
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $user_id = Cache::remember("Wallet".$this->data['deposit']->wallet_id,60, function(){
            return Wallet::where('id',$this->data['deposit']->wallet_id)->first();
        })->user_id;
        return array(new PrivateChannel('balances.'.$user_id));
    }
}
