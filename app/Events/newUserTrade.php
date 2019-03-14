<?php

namespace App\Events;

use App\Coin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Market;
use Illuminate\Support\Facades\Cache;
use InfluxDB\Database\Exception;
use TrayLabs\InfluxDB\Facades\InfluxDB;
use InfluxDB\Point;
use InfluxDB\Database;
class newUserTrade implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public function __construct($trade)
    {
        $market = Cache::remember("Market" . $trade->market_id, 60, function () use ($trade) {
            return Market::where('id', $trade->market_id)->first();
        });

        $trade->market = $market->base_coin->name . "/" . $market->trade_coin->name;
        if (gettype($trade) == 'object') {
            $trade = $trade->formatDates();
        }

        $this->data = ["trade" => $trade];
        $user_id_taker = $this->data['trade']->user_id_taker;
        $user_id_maker = $this->data['trade']->user_id_maker;
        $coin_name = Coin::where('id', $market->trade_currency_id)->first()->name;

        $type = $this->data['trade']->type;
        if ($type == 'buy') {
            $str = 'bought';
        } else if ($type == 'sell') {
            $str = 'sold';
        }
        $text = "User " . $user_id_maker . " successfully " . $str . " " . $this->data['trade']->amount . " " . $coin_name . " from User " . $user_id_taker;
        list($usec, $sec) = explode(' ', microtime());
        $timestamp = sprintf('%d%06d', $sec, $usec*1000000);
        try {
            $points = array(new Point
            (
                'live_update',
                null,
                ['type' => 'trade'],
                ['text' => $text],
                $timestamp
            ));
            InfluxDB::writePoints($points,Database::PRECISION_MICROSECONDS);
        } catch (Exception $e) {

        }
    }

    public function broadcastAs()
    {
        return "NewUserTrade";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastOn()
    {
        return array(new PrivateChannel('trades.' . $this->data['trade']->user_id_taker));
    }
}
