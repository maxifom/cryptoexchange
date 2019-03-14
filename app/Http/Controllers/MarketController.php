<?php

namespace App\Http\Controllers;

use App\Coin;
use App\Market;
use App\Trade;
use Illuminate\Support\Collection;
use App\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as JavaScript;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use TrayLabs\InfluxDB\Facades\InfluxDB;
use Carbon\Carbon;

class MarketController extends Controller
{
    public function market($base_coin, $trade_coin)
    {

        if (Auth::user() !== null) {
            $timezone = Auth::user()->timezone;
        } else {
            $timezone = 'UTC';
        }

        $base_currency_id = Cache::remember('Coin' . $base_coin, 60, function () use ($base_coin) {
            return Coin::where('name', $base_coin)->first();
        })->id;
        $trade_currency = Cache::remember('Coin' . $trade_coin, 60, function () use ($trade_coin) {
            return Coin::where('name', $trade_coin)->first();
        });

        $trade_currency_id = $trade_currency->id;
        $name = $base_coin . $trade_coin;
        $market=null;
        //Cache::forget('Market' .$name);
        if (Cache::has('Market'.$name)) {
            $market = Cache::get('Market'.$name);
            if (!$market->confirmed) {
                return redirect('wallets');
            }
            if (!$market) {
                return redirect('wallets');
            }
        }
        else
        {
            $market = Market::where('base_currency_id', $base_currency_id)->where('trade_currency_id', $trade_currency_id)->first();
            if (!$market->confirmed) {
                return redirect('wallets');
            }
            if (!$market) {
                return redirect('wallets');
            }
            $sell_trades = Trade::where('market_id', $market->id)->where('type', 'sell')->where('finished', false)->orderBy('price')->get();
            $buy_trades =Trade::where('market_id', $market->id)->where('type', 'buy')->where('finished', false)->orderByDesc('price')->get();
            $sell_trades = $sell_trades->groupBy('price');
            $sell_trades_final = new Collection;
            $buy_trades_final = new Collection;
            $buy_trades = $buy_trades->groupBy('price');
            $i = 0;
            //super fast
            foreach ($buy_trades as $price) {
                if ($i === 50) {
                    break;
                }
                $buy_trades_final->push(['amount' => $price->sum('amount'), 'price' => $price[0]->price, 'market_id' => $price[0]->market_id]);
                $i++;
            }
            $i = 0;
            foreach ($sell_trades as $price) {
                if ($i === 50) {
                    break;
                }
                $sell_trades_final->push(['amount' => $price->sum('amount'), 'price' => $price[0]->price, 'market_id' => $price[0]->market_id]);
                $i++;
            }
            //
            $market->sell_trades = $sell_trades_final;
            $market->buy_trades = $buy_trades_final;
            $count_trades = 0;
            $c = InfluxDB::query("SELECT COUNT(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
            if ($c !== null && isset($c[0]['count']) && $c[0]['count'] > 0) {
                $count_trades = $c[0]['count'];
                $q = InfluxDB::query("SELECT MIN(value),MAX(value),LAST(value),FIRST(value),SUM(total) as sum_total,SUM(amount) as sum_amount FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                /*$low = InfluxDB::query("SELECT MIN(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $high = InfluxDB::query("SELECT MAX(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $close = InfluxDB::query("SELECT LAST(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $open = InfluxDB::query("SELECT FIRST(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $volume_base = InfluxDB::query("SELECT SUM(total) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $volume_trade = InfluxDB::query("SELECT SUM(amount) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();*/
                $market->low = number_format($q[0]['min'], 8, '.', '');
                $market->close = number_format($q[0]['last'], 8, '.', '');
                $market->high = number_format($q[0]['max'], 8, '.', '');
                $market->open = number_format($q[0]['first'], 8, '.', '');
                $market->volume_base = number_format($q[0]['sum_total'], 8, '.', '');
                $market->volume_trade = number_format($q[0]['sum_amount'], 8, '.', '');
            } else {
                $market->low = 0;
                $market->close = 0;
                $market->high = 0;
                $market->open = 0;
                $market->volume_base = 0;
                $market->volume_trade = 0;
            }
            $market->trade_count_24hrs = $count_trades;
            Cache::put('Market'.$name, $market, 5);
        }
        $first_time = InfluxDB::query("SELECT FIRST(value) FROM trades WHERE market_id='" . $market->id . "'")->getPoints();
        $low = [];
        $high = [];
        $close = [];
        $open = [];
        $x = [];
        $mins = [];
        $maxs = [];
        $last = [];
        $first = [];
        /** @noinspection TypeUnsafeComparisonInspection */
        if ($first_time != null) {
            $time = strtotime($first_time[0]['time']);
            $len = \strlen($time);
            $draw_graph = true;
            while ($len < 19) {
                $time .= '0';
                $len++;
            }

            $q = InfluxDB::query('SELECT MIN(value),MAX(value),LAST(value),FIRST(value) FROM trades WHERE time>=now()-1d AND time>=' . $time . " AND market_id='" . $market->id . "' GROUP BY time(30m)")->getPoints();
            /*$mins = InfluxDB::query('SELECT MIN(value) FROM trades WHERE time>=now()-1d AND time>=' . $time . " AND market_id='" . $market->id . "' GROUP BY time(30m)")->getPoints();
            $maxs = InfluxDB::query('SELECT MAX(value) FROM trades WHERE time>=now()-1d AND time>=' . $time . " AND market_id='" . $market->id . "' GROUP BY time(30m)")->getPoints();
            $last = InfluxDB::query('SELECT LAST(value) FROM trades WHERE time>=now()-1d AND time>=' . $time . " AND market_id='" . $market->id . "' GROUP BY time(30m)")->getPoints();
            $first = InfluxDB::query('SELECT FIRST(value) FROM trades WHERE time>=now()-1d AND time>=' . $time . " AND market_id='" . $market->id . "' GROUP BY time(30m)")->getPoints();
            */
            $last_last = InfluxDB::query("SELECT LAST(value) FROM trades WHERE market_id='" . $market->id . "'")->getPoints();
            if ($last_last !== null) {
                $last_last = $last_last[0]['last'];
            }
            for ($i = 0, $iMax = count($q); $i < $iMax; $i++) {
                if ($q[$i]['min'] !== null) {
                    $low[$i] = $q[$i]['min'];
                    $high[$i] = $q[$i]['max'];
                    $close[$i] = $q[$i]['last'];
                    $open[$i] = $q[$i]['first'];
                } else {
                    $low[$i] = 0;
                    $set = false;
                    for ($j = $i; $j >= 0; $j--) {
                        if ($q[$j]['min'] !== null) {
                            $low[$i] = $q[$j]['last'];
                            $close[$i] = $q[$j]['last'];
                            $high[$i] = $q[$j]['last'];
                            $open[$i] = $q[$j]['last'];
                            $set = true;
                            break;
                        }

                    }
                    if (!$set) {
                        $low[$i] = $last_last;
                        $close[$i] = $last_last;
                        $high[$i] = $last_last;
                        $open[$i] = $last_last;
                    }
                }
                $x[$i] = Carbon::createFromTimeString($q[$i]['time'])->tz($timezone)->format('Y-m-d H:i:s');
            };
            if (empty($q)) {
                $start_timestamp = Carbon::now()->subDay()->getTimestamp();
                $start_timestamp = (int)(round($start_timestamp / 30 / 60) * 30 * 60);
                for ($i = 0; $i < 48; $i++) {
                    $x[$i] = Carbon::createFromTimestampUTC($start_timestamp + $i * 30 * 60)->tz($timezone)->format('Y-m-d H:i:s');
                    $low[$i] = $last_last;
                    $close[$i] = $last_last;
                    $high[$i] = $last_last;
                    $open[$i] = $last_last;
                }
            }
        } else {
            $draw_graph = false;
        }
        $market_trades = InfluxDB::query("SELECT time,value,type,amount FROM trades WHERE market_id = '" . $market->id . "' ORDER BY time DESC LIMIT 50")->getPoints();
        if ($market_trades !== null) {
            for ($i = 0, $iMax = \count($market_trades); $i < $iMax; $i++) {
                $market_trades[$i]['updated_at'] = Carbon::createFromTimeString($market_trades[$i]['time'])->tz($timezone)->toDateTimeString();
                $market_trades[$i]['price'] = $market_trades[$i]['value'];
                unset($market_trades[$i]['time'], $market_trades[$i]['value']);
            }
        }
        if (Auth::id() !== NULL) {

            $user_trades = InfluxDB::query("SELECT time,value,type,amount FROM trades WHERE market_id = '" . $market->id . "' AND (user_id_taker='" . Auth::id() . "' OR user_id_maker='" . Auth::id() . "') ORDER BY time DESC LIMIT 25")->getPoints();
            if ($user_trades !== null) {
                for ($i = 0, $iMax = \count($user_trades); $i < $iMax; $i++) {
                    $user_trades[$i]['created_at'] = Carbon::createFromTimeString($user_trades[$i]['time'])->tz($timezone)->toDateTimeString();
                    $user_trades[$i]['price'] = $user_trades[$i]['value'];
                    unset($user_trades[$i]['time'], $user_trades[$i]['value']);
                }
            }
        } else {
            $user_trades = NULL;
        }
        $data = [
            'x' => $x,
            'open' => $open,
            'close' => $close,
            'low' => $low,
            'high' => $high,
            'increasing' => ['line' => ['color' => '#28a745']],
            'decreasing' => ['line' => ['color' => '#E52B50']],
            'line' => ['color' => 'rgba(31,119,180,1)'],
            'type' => 'candlestick',
            'xaxis' => 'x',
            'yaxis' => 'y'
        ];
        //Cache::forget('Markets');
        if (Cache::has('Markets')) {
            $markets = Cache::get('Markets');
        } else {
            $markets = Market::where('confirmed', 1)->select('id', 'base_currency_id', 'trade_currency_id')->with(
                ['base_coin' => function ($query) {
                    $query->select(['id','name']);
                },
                    'trade_coin' => function ($query) {
                        $query->select(['id','name']);
                    }]
            )->get();
            foreach ($markets as $_market) {
                $_market->name = $_market->base_coin->name . '/' . $_market->trade_coin->name;
                $_market->trade_name = $_market->trade_coin->name;
                $_market->low = 0;
                $_market->close = 0;
                $_market->high = 0;
                $_market->open = 0;
                $_market->volume_base = 0;
                $_market->volume_trade = 0;
                $_market->trade_count_24hrs = 0;
            }
            $c = InfluxDB::query('SELECT COUNT(value) FROM trades WHERE time>=now()-1d GROUP BY market_id');
            if ($c) {
                $count_points = $c->getPoints();
                $count_series = $c->getSeries();
                $volume_bases = InfluxDB::query('SELECT SUM(total) FROM trades WHERE time>=now()-1d GROUP BY market_id')->getPoints();
                for ($i = 0, $iMax = \count($count_series); $i < $iMax; $i++) {
                    $market_id = $count_series[$i]['tags']['market_id'];
                    $market = $markets->firstWhere('id', $market_id);
                    $market->trade_count_24hrs = $count_points[$i]['count'];
                    $market->volume_base = $volume_bases[$i]['sum'];
                }
            }
            $markets = $markets->sortByDesc('volume_base')->values()->all();
            Cache::put('Markets', $markets, 5);
        }
        if (Auth::user() !== null) {
            $notification_enabled = Auth::user()->notification_enabled;
            $wallet_base_full = Cache::remember('WalletUser' . Auth::id() . 'Coin' . $market->base_currency_id, 60, function () use ($market) {
                return Wallet::where('coin_id', $market->base_currency_id)->where('user_id', Auth::id())->first();
            });
            $wallet_trade_full = Cache::remember('WalletUser' . Auth::id() . 'Coin' . $market->trade_currency_id, 60, function () use ($market) {
                return Wallet::where('coin_id', $market->trade_currency_id)->where('user_id', Auth::id())->first();
            });
            $wallet_base = new Wallet;
            $wallet_trade = new Wallet;
            $wallet_base->balance = $wallet_base_full->balance;
            $wallet_trade->balance = $wallet_trade_full->balance;
            $wallet_base->name = Cache::remember('Coin' . $wallet_base_full->coin_id, 60, function () use ($wallet_base_full) {
                return Coin::where('id', $wallet_base_full->coin_id)->first();
            })->name;
            $wallet_trade->name = Cache::remember('Coin' . $wallet_trade_full->coin_id, 60, function () use ($wallet_trade_full) {
                return Coin::where('id', $wallet_trade_full->coin_id)->first();
            })->name;
        } else {
            $wallet_base = null;
            $wallet_trade = null;
            $notification_enabled = 0;
        }


        $coin_meta = $trade_currency->meta;
        Javascript::put([
            'market_id' => $market->id,
            'user_id' => Auth::id() ?: null,
            'buy_trades' => $market->buy_trades,
            'sell_trades' => $market->sell_trades,
            'draw_graph' => $draw_graph,
            'data' => $data,
            'market_trades' => $market_trades,
            'user_trades' => $user_trades,
            'markets' => $markets,
            'wallet_base' => $wallet_base,
            'wallet_trade' => $wallet_trade,
            'notification_enabled' => $notification_enabled,
            'fee' => $trade_currency->trading_fee->fee ?: 0
        ]);
        return view('exchange')->with(['market' => $market, 'base_coin' => $base_coin, 'trade_coin' => $trade_coin, 'meta' => $coin_meta, 'draw_graph' => $draw_graph]);
    }

    public function getTrades(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'market_id' => 'required|integer',
            'count' => 'required|integer'
        ]);
        $type = $request->post('type');
        $market_id = $request->post('market_id');
        $count = $request->post('count');
        if ($type === 'sell') {
            $sell_trades = Trade::where('market_id', $market_id)->where('type', 'sell')->where('finished', false)->orderBy('price')->get();
            $sell_trades_count = $sell_trades->count();
            if ($sell_trades_count === 0 || $sell_trades_count <= $count) {
                return -1;
            }
            $sell_trades = $sell_trades->groupBy('price');
            $sell_trades_final = new Collection;
            $i = 0;
            foreach ($sell_trades as $price) {
                if ($i == 50) {
                    break;
                }
                $sell_trades_final->push(['amount' => $price->sum('amount'), 'price' => $price[0]->price, 'market_id' => $price[0]->market_id]);
                $i++;
            }
            return json_encode(['trades' => $sell_trades_final]);
        }

        if ($type === 'buy') {
            $buy_trades = Trade::where('market_id', $market_id)->where('type', 'buy')->where('finished', false)->orderByDesc('price')->get();
            $buy_trades_count = $buy_trades->count();
            if ($buy_trades_count === 0 || $buy_trades_count <= $count) {
                return -1;
            }
            $buy_trades_final = new Collection;
            $buy_trades = $buy_trades->groupBy('price');
            $i = 0;
            foreach ($buy_trades as $price) {
                if ($i === 50) {
                    break;
                }
                $buy_trades_final->push(['amount' => $price->sum('amount'), 'price' => $price[0]->price, 'market_id' => $price[0]->market_id]);
                $i++;
            }
            return json_encode(['trades' => $buy_trades_final]);
        }


    }

    public function markets()
    {
        //Cache::forget('Markets');
        if (Cache::has('Markets')) {
            $markets = Cache::get('Markets');
        } else {
            $markets = Market::where('confirmed', 1)->select('id', 'base_currency_id', 'trade_currency_id')->with(
                ['base_coin' => function ($query) {
                    $query->select(['id','name']);
                },
                    'trade_coin' => function ($query) {
                        $query->select(['id','name']);
                    }]
            )->get();
            foreach ($markets as $_market) {
                $_market->name = $_market->base_coin->name . '/' . $_market->trade_coin->name;
                $_market->base_name = $_market->base_coin->name;
                $_market->trade_name = $_market->trade_coin->name;
                $_market->low = 0;
                $_market->close = 0;
                $_market->high = 0;
                $_market->open = 0;
                $_market->volume_base = 0;
                $_market->volume_trade = 0;
                $_market->trade_count_24hrs = 0;
            }
            $c = InfluxDB::query('SELECT COUNT(value) FROM trades WHERE time>=now()-1d GROUP BY market_id');
            if ($c) {
                $count_points = $c->getPoints();
                $count_series = $c->getSeries();
                $volume_bases = InfluxDB::query('SELECT SUM(total) FROM trades WHERE time>=now()-1d GROUP BY market_id')->getPoints();
                for ($i = 0, $iMax = count($count_series); $i < $iMax; $i++) {
                    $market_id = $count_series[$i]['tags']['market_id'];
                    $market = $markets->firstWhere('id', $market_id);
                    $market->trade_count_24hrs = $count_points[$i]['count'];
                    $market->volume_base = $volume_bases[$i]['sum'];
                }
            }
            $markets = $markets->sortByDesc('volume_base')->values()->all();
            Cache::put('Markets', $markets, 5);
        }
        if (Auth::user()) {
            $notification_enabled = auth()->user()->notification_enabled;
        } else {
            $notification_enabled = 0;
        }
        Javascript::put([
            'markets' => $markets,
            'user_id' => Auth::id(),
            'notification_enabled' => $notification_enabled
        ]);
        return view('markets');
    }

    public function main()
    {
        return view('main');
    }
}
