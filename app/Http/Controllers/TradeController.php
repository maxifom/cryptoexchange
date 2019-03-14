<?php

namespace App\Http\Controllers;

use App\Events\TradeSuccessful;
use App\UserTrade;
use App\Trade;
use App\Market;
use Illuminate\Support\Facades\Cache;
use App\Wallet;
use Illuminate\Support\Collection;
use App\MarketHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Events\TradeDeleted;
use App\Events\NewTrade as newTrade;
use Illuminate\Support\Facades\Auth;
use App\Events\newUserTrade;
use App\Coin;
use InfluxDB\Database;
use InfluxDB\Point;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class TradeController extends Controller
{
    public function deleteTrade(Request $request)
    {
        $request->validate([
            'trade' => 'required'
        ]);
        return DB::transaction(function () use ($request) {
            if ($trade = Trade::find($request->post('trade')))
                if (Auth::id() === $trade->user_id && $trade->finished === 0) {
                    $user = Auth::user();
                    if ($trade->type == 'sell') {
                        $trade_coin_id = Cache::remember("Market" . $trade->market_id, 60, function () use ($trade) {
                            return Market::where('id', $trade->market_id)->first();
                        })->trade_currency_id;
                        $wallet = Cache::remember("WalletUser" . $user->id . "Coin" . $trade_coin_id, 60, function () use ($trade_coin_id, $user) {
                            return Wallet::where('coin_id', $trade_coin_id)->where('user_id', $user->id)->first();
                        });
                        $wallet->balance += $trade->amount;
                        $wallet->save();
                    } else {
                        if ($trade->type == 'buy') {
                            $base_coin_id = Cache::remember("Market" . $trade->market_id, 60, function () use ($trade) {
                                return Market::where('id', $trade->market_id)->first();
                            })->base_currency_id;
                            $trade_coin_id = Cache::remember("Market" . $trade->market_id, 60, function () use ($trade) {
                                return Market::where('id', $trade->market_id)->first();
                            })->trade_currency_id;
                            $trade_coin = Coin::find($trade_coin_id);
                            $fee = $trade_coin->trading_fee->fee;
                            $wallet = Cache::remember("WalletUser" . $user->id . "Coin" . $base_coin_id, 60, function () use ($base_coin_id, $user) {
                                return Wallet::where('coin_id', $base_coin_id)->where('user_id', $user->id)->first();
                            });
                            $_fee = $trade->fee - $trade->fee_sub;
                            $wallet->balance += $trade->amount * $trade->price + $_fee;
                            $wallet->save();
                        }
                    }
                    $trade->delete();
                    event(new TradeDeleted($trade));
                    return json_encode(['status' => 'Trade deleted successful']);
                }
        }, 1);

    }

    public function makeTrade(Request $request)
    {
        $request->validate([
            'price' => 'required|min:0.00000001',
            'amount' => 'required',
            'market_id' => 'required|integer',
            'type' => 'required'
        ]);
        try {
            return DB::transaction(function () use ($request) {

                $points = [];
                $events = new Collection;
                $round_numerator = 60 * 30;
                $rounded_time = round(time() / $round_numerator) * $round_numerator;
                $rounded_date = date("Y-m-d H:i:s", $rounded_time);
                $prev_date = date("Y-m-d H:i:s", $rounded_time - $round_numerator);
                $price = $request->post('price');
                $amount = $request->post('amount');
                $market_id = $request->post('market_id');
                $type = $request->post('type');
                $user = Auth::user();
                $market = Market::where('id', $market_id)->first();
                $trade_coin = Coin::where('id', $market->trade_currency_id)->first();
                if (!$market->confirmed) {
                    return -1;
                }
                $fee = $trade_coin->trading_fee->fee;
                $minimal_trade = 300e-8;/*number_format((1e-8/((float)$fee)),8,'.','');*/
                if ($price * $amount < $minimal_trade) {
                    return json_encode(['status' => 'Trade amount is less than minimum amount (' . number_format($minimal_trade,8,'.','') . ')']);
                }
                //$fee = 0.2 / 100;
                $wallet_taker_base = Wallet::where('coin_id', $market->base_currency_id)->where('user_id', $user->id)->lockForUpdate()->first();
                $wallet_taker_trade = Wallet::where('coin_id', $market->trade_currency_id)->where('user_id', $user->id)->lockForUpdate()->first();
                if ($type == 'buy') {                               //buy SCORE for BTC

                    if ($wallet_taker_base->balance < $amount * $price * $fee && $wallet_taker_base->balance >= 0.00000001) {
                        return json_encode(['status' => 'Trade amount is more than balance']);
                        //$amount = floatval(number_format($wallet_taker_base->balance / $price, 8, '.', ''));
                    }
                    $trades = Trade::where('market_id',$market_id)->where("price", "<=", $price)->where('type', 'sell')->where('finished', false)->where("user_id", "<>", $user->id)->orderBy('price')->orderBy('id')->lockForUpdate()->get();
                    if ($trades->isEmpty()) {

                        return $this->addTrade($amount, $price, $type, $market);
                    }
                    $all_fee = $amount * $price * $fee;
                    if ($all_fee < 1e-8) {
                        $all_fee = 1e-8;
                    }
                    $prices = $trades->groupBy('price');
                    $sum = 0;
                    $needed_trades = new Collection;
                    foreach ($prices as $_price) {
                        foreach ($_price as $trade) {
                            $sum += floatval(number_format($trade->amount, 8, '.', ''));    //number format not needed
                            $needed_trades->push($trade);
                            if ($sum >= $amount) {
                                break;
                            }
                        }
                        if ($sum >= $amount) {
                            //dump($sum);
                            break;
                        }
                    }
                    /*dump('needed',$needed_trades);
                    dump($prices);
                    dump($_GET);*/
                    $taker_base_saldo = 0;
                    $taker_trade_saldo = 0;
                    foreach ($needed_trades as $trade) {
                        if ($trade->amount <= $amount) {
                            $total = $trade->amount * $trade->price;
                            $num_fee = $total * $fee;
                            if ($num_fee < 1e-8) {
                                $num_fee = 1e-8;
                            }
                            if ($all_fee >= 1e-8) {
                                if ($num_fee >= $all_fee) {
                                    $num_fee = $all_fee;
                                    $all_fee = 0;
                                } else {
                                    $all_fee -= $num_fee;
                                }
                            } else {
                                $num_fee = 0;
                            }
                            $taker_base_saldo -= $total + $num_fee/*$trade->amount * $trade->price * (1 + $fee)*/
                            ;
                            $taker_trade_saldo += $trade->amount;
                            //$wallet_taker_base->balance -= $trade->amount * $trade->price;
                            //$wallet_taker_trade->balance += $trade->amount * (1 - $fee);
                            $wallet_maker = Wallet::where('user_id', $trade->user_id)->where("coin_id", $market->base_currency_id)->lockForUpdate()->first();
                            $_fee = $total * $fee;
                            if ($trade->fee_sub < $trade->fee) {
                                if ($_fee >= $trade->fee) {
                                    $_fee = $trade->fee;
                                    $trade->fee_sub = $_fee;
                                } else {
                                    $trade->fee_sub += $_fee;
                                }
                            } else {
                                $_fee = 0;
                            }
                            $wallet_maker->balance += $total - $_fee;
                            $amount -= $trade->amount;
                            $trade->user_id_taker = $user->id;
                            $trade->finished = true;
                            $trade->save();
                            //$wallet_taker_base->save();
                            //$wallet_taker_trade->save();
                            $wallet_maker->save();
                            /*//MARKET STUFF
                            $mh = MarketHistory::where("market_time", $rounded_date)->where('market_id', $market_id)->first();
                            $prevMarket = MarketHistory::where("market_time", $prev_date)->where('market_id', $market_id)->first();
                            if ($mh) {
                                if ($trade->price < $mh->low) {
                                    $mh->low = $trade->price;
                                } else if ($trade->price > $mh->high) {
                                    $mh->high = $trade->price;
                                }
                                $mh->close = $trade->price;
                                $mh->trade_count++;
                                $mh->volume += $trade->amount;
                                $mh->volume_base += $trade->amount * $trade->price;
                                $mh->save();
                            } else {
                                $open = 0;
                                if ($prevMarket) {
                                    $open = $prevMarket->close;
                                } else {
                                    $open = $trade->price;
                                }
                                MarketHistory::create(['market_id' => $market_id, 'high' => $trade->price, 'low' => $trade->price, 'close' => $trade->price, 'market_time' => $rounded_date, 'open' => $open, 'volume' => $trade->amount, 'volume_base' => $trade->amount * $trade->price, 'trade_count' => 1]);
                            }
                            //MARKET STUFF*/
                            $ut = new UserTrade;
                            $ut->trade_id = $trade->id;
                            $ut->user_id_maker = $trade->user_id;
                            $ut->user_id_taker = $user->id;
                            $ut->type = $trade->type;
                            $ut->price = $trade->price;
                            $ut->amount = $trade->amount;
                            $ut->market_id = $market_id;
                            $time = time();
                            list($usec, $sec) = explode(' ', microtime());
                            $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
                            $ut->created_at = $time;
                            $ut->updated_at = $time;
                            $points[] = new Point(
                                'trades',
                                floatval($ut->price),
                                [
                                    'trade_id' => $ut->trade_id,
                                    'type' => $ut->type,

                                    'market_id' => $ut->market_id,
                                    'user_id_taker' => $ut->user_id_taker,
                                    'user_id_maker' => $ut->user_id_maker],
                                ['amount' => floatval($ut->amount),
                                    'total' => floatval($ut->amount) * floatval($ut->price)],
                                $timestamp);
                            /*$ut = UserTrade::create([
                                "trade_id" => $trade->id,
                                "user_id_maker" => $trade->user_id,
                                "user_id_taker" => $user->id,
                                "type" => $trade->type,
                                "price" => $trade->price,
                                "amount" => $trade->amount,
                                "market_id" => $market_id,
                            ]);*/
                            $events->push(new newUserTrade($ut));
                            $events->push(new TradeSuccessful($trade, $trade->amount));


                            // dump("Bought " . number_format($trade->amount, 8, '.', '') . " " . $trade_coin->name . " for " . number_format($total, 8, '.', '') . " " . $base_coin->name);
                            //dump("Fee:" . number_format($trade->amount * ($fee), 8, '.', '') . " " . $trade_coin->name . " + " . number_format($total * ($fee), 8, '.', '') . " BTC");
                        } else {
                            $total = $amount * $trade->price;
                            $num_fee = $total * $fee;
                            if ($num_fee < 1e-8) {
                                $num_fee = 1e-8;
                            }
                            if ($all_fee >= 1e-8) {
                                if ($num_fee >= $all_fee) {
                                    $num_fee = $all_fee;
                                    $all_fee = 0;
                                } else {
                                    $all_fee -= $num_fee;
                                }
                            } else {
                                $num_fee = 0;
                            }
                            $taker_base_saldo -= $total + $num_fee;
                            //$wallet_taker_base->balance -= $amount * $trade->price;
                            $wallet_maker = Wallet::where('user_id', $trade->user_id)->where("coin_id", $market->base_currency_id)->lockForUpdate()->first();
                            $_fee = $total * $fee;
                            if ($trade->fee_sub < $trade->fee) {
                                if ($_fee >= $trade->fee) {
                                    $_fee = $trade->fee;
                                    $trade->fee_sub = $_fee;
                                } else {
                                    $trade->fee_sub += $_fee;
                                }
                            } else {
                                $_fee = 0;
                            }
                            $wallet_maker->balance += $total - $_fee;
                            //$wallet_taker_trade->balance += $amount * (1 - $fee);
                            $taker_trade_saldo += $amount;
                            $trade->amount -= $amount;
                            $amount1 = $amount;
                            $amount = 0;
                            if (floatval(number_format($trade->amount, 8, '.', '')) <= 0.00000001) {
                                $trade->finished = true;
                            }
                            $trade->save();
                            //$wallet_taker_base->save();
                            //$wallet_taker_trade->save();
                            $wallet_maker->save();
                            /*//MARKET STUFF
                            $mh = MarketHistory::where("market_time", $rounded_date)->where('market_id', $market_id)->first();
                            $prevMarket = MarketHistory::where("market_time", $prev_date)->where('market_id', $market_id)->first();
                            if ($mh) {
                                if ($trade->price < $mh->low) {
                                    $mh->low = $trade->price;
                                } else if ($trade->price > $mh->high) {
                                    $mh->high = $trade->price;
                                }
                                $mh->close = $trade->price;
                                $mh->trade_count++;
                                $mh->volume += $trade->amount;
                                $mh->volume_base += $trade->amount * $trade->price;
                                $mh->save();
                            } else {
                                $open = 0;
                                if ($prevMarket) {
                                    $open = $prevMarket->close;
                                } else {
                                    $open = $trade->price;
                                }
                                MarketHistory::create(['market_id' => $market_id, 'high' => $trade->price, 'low' => $trade->price, 'close' => $trade->price, 'market_time' => $rounded_date, 'open' => $open, 'volume' => $trade->amount, 'volume_base' => $trade->amount * $trade->price, 'trade_count' => 1]);
                            }


                            //MARKET STUFF*/
                            /*$ut = UserTrade::create([
                                "trade_id" => $trade->id,
                                "user_id_maker" => $trade->user_id,
                                "user_id_taker" => $user->id,
                                "type" => $trade->type,
                                "price" => $trade->price,
                                "amount" => $amount1,
                                "market_id" => $market_id,
                            ]);*/
                            $ut = new UserTrade;
                            $ut->trade_id = $trade->id;
                            $ut->user_id_maker = $trade->user_id;
                            $ut->user_id_taker = $user->id;
                            $ut->type = $trade->type;
                            $ut->price = $trade->price;
                            $ut->amount = $amount1;
                            $ut->market_id = $market_id;
                            $time = time();
                            list($usec, $sec) = explode(' ', microtime());
                            $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
                            $ut->created_at = $time;
                            $ut->updated_at = $time;
                            $points[] = new Point(
                                'trades',
                                floatval($ut->price),
                                [
                                    'trade_id' => $ut->trade_id,
                                    'type' => $ut->type,
                                    'market_id' => $ut->market_id,
                                    'user_id_taker' => $ut->user_id_taker,
                                    'user_id_maker' => $ut->user_id_maker],
                                ['amount' => floatval($ut->amount), 'total' => floatval($ut->amount) * floatval($ut->price)
                                ],
                                $timestamp);
                            $events->push(new newUserTrade($ut));
                            $events->push(new TradeSuccessful($trade, $amount1));


                            //dump("Bought " . number_format($amount1, 8, '.', '') . " " . $trade_coin->name . " for " . number_format($total, 8, '.', '') . " " . $base_coin->name);
                            //dump("Fee:" . number_format($amount1 * ($fee), 8, '.', '') . " " . $trade_coin->name . " + " . number_format($total * ($fee), 8, '.', '') . " BTC");
                        }
                    }
                    $wallet_taker_trade->balance += $taker_trade_saldo;
                    $wallet_taker_base->balance += $taker_base_saldo;
                    $wallet_taker_base->save();
                    $wallet_taker_trade->save();
                } else if ($type == 'sell') { //sell SCORE for BTC

                    $taker_base_saldo = 0;
                    $taker_trade_saldo = 0;
                    if ($wallet_taker_trade->balance < $amount && $wallet_taker_trade->balance >= 0.00000001) {
                        return json_encode(['status' => 'Trade amount is more than balance']);
                    }
                    $trades = Trade::where('market_id',$market_id)->where("price", ">=", $price)->where('type', 'buy')->where('finished', false)->where("user_id", "<>", $user->id)->orderByDesc('price')->orderBy('id')->lockForUpdate()->get();
                    if ($trades->isEmpty()) {
                        return $this->addTrade($amount, $price, $type, $market);

                    }
                    $all_fee = $amount * $price * $fee;
                    if ($all_fee < 1e-8) {
                        $all_fee = 1e-8;
                    }
                    $prices = $trades->groupBy('price');
                    $sum = 0;
                    $needed_trades = new Collection;
                    foreach ($prices as $_price) {
                        foreach ($_price as $trade) {
                            $sum += floatval(number_format($trade->amount, 8, '.', ''));
                            $needed_trades->push($trade);
                            if ($sum >= $amount) {
                                break;
                            }
                        }
                        if ($sum >= $amount) {
                            //dump($sum);
                            break;
                        }
                    }
                    /*dump('needed',$needed_trades);
                    dump($prices);
                    dump($_GET);*/
                    foreach ($needed_trades as $trade) {
                        if ($trade->amount <= $amount) {
                            $total = $trade->amount * $trade->price;
                            $num_fee = $total * $fee;
                            if ($num_fee < 1e-8) {
                                $num_fee = 1e-8;
                            }
                            if ($all_fee >= 1e-8) {
                                if ($num_fee >= $all_fee) {
                                    $num_fee = $all_fee;
                                    $all_fee = 0;
                                } else {
                                    $all_fee -= $num_fee;
                                }
                            } else {
                                $num_fee = 0;
                            }
                            $taker_base_saldo += $total - $num_fee;
                            $taker_trade_saldo -= $trade->amount;
                            //$wallet_taker_base->balance += $total * (1 - $fee);
                            //$wallet_taker_trade->balance -= $trade->amount;
                            $wallet_maker = Wallet::where('user_id', $trade->user_id)->where("coin_id", $market->trade_currency_id)->lockForUpdate()->first();
                            $wallet_maker->balance += $trade->amount;
                            $amount -= $trade->amount;
                            $trade->user_id_taker = $user->id;
                            $trade->finished = true;
                            $trade->save();
                            //$wallet_taker_base->save();
                            //$wallet_taker_trade->save();
                            $wallet_maker->save();
                            /*//MARKET STUFF
                            $mh = MarketHistory::where("market_time", $rounded_date)->where('market_id', $market_id)->first();
                            $prevMarket = MarketHistory::where("market_time", $prev_date)->where('market_id', $market_id)->first();
                            if ($mh) {
                                if ($trade->price < $mh->low) {
                                    $mh->low = $trade->price;
                                } else if ($trade->price > $mh->high) {
                                    $mh->high = $trade->price;
                                }
                                $mh->close = $trade->price;
                                $mh->trade_count++;
                                $mh->volume += $trade->amount;
                                $mh->volume_base += $trade->amount * $trade->price;
                                $mh->save();
                            } else {
                                $open = 0;
                                if ($prevMarket) {
                                    $open = $prevMarket->close;
                                } else {
                                    $open = $trade->price;
                                }
                                MarketHistory::create(['market_id' => $market_id, 'high' => $trade->price, 'low' => $trade->price, 'close' => $trade->price, 'market_time' => $rounded_date, 'open' => $open, 'volume' => $trade->amount, 'volume_base' => $trade->amount * $trade->price, 'trade_count' => 1]);
                            }


                            //MARKET STUFF*/
                            /* $ut = UserTrade::create([
                                 "trade_id" => $trade->id,
                                 "user_id_maker" => $trade->user_id,
                                 "user_id_taker" => $user->id,
                                 "type" => $trade->type,
                                 "price" => $trade->price,
                                 "amount" => $trade->amount,
                                 "market_id" => $market_id,
                             ]);*/
                            $ut = new UserTrade;
                            $ut->trade_id = $trade->id;
                            $ut->user_id_maker = $trade->user_id;
                            $ut->user_id_taker = $user->id;
                            $ut->type = $trade->type;
                            $ut->price = $trade->price;
                            $ut->amount = $trade->amount;
                            $ut->market_id = $market_id;
                            $time = time();
                            list($usec, $sec) = explode(' ', microtime());
                            $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
                            $ut->created_at = $time;
                            $ut->updated_at = $time;
                            $points[] = new Point(
                                'trades',
                                floatval($ut->price),
                                [
                                    'trade_id' => $ut->trade_id,
                                    'type' => $ut->type,
                                    'market_id' => $ut->market_id,
                                    'user_id_taker' => $ut->user_id_taker,
                                    'user_id_maker' => $ut->user_id_maker],
                                ['amount' => floatval($ut->amount),
                                    'total' => floatval($ut->amount) * floatval($ut->price)
                                ],
                                $timestamp);
                            $events->push(new newUserTrade($ut));
                            $events->push(new TradeSuccessful($trade, $trade->amount));


                            // dump("Sold " . number_format($trade->amount, 8, '.', '') . " " . $trade_coin->name . " for " . number_format($total, 8, '.', '') . " " . $base_coin->name);
                            // dump("Fee:" . number_format($trade->amount * ($fee), 8, '.', '') . " " . $trade_coin->name . " + " . number_format($total * ($fee), 8, '.', '') . " BTC");
                        } else {
                            $total = $amount * $trade->price;
                            $num_fee = $total * $fee;
                            if ($num_fee < 1e-8) {
                                $num_fee = 1e-8;
                            }
                            if ($all_fee >= 1e-8) {
                                if ($num_fee >= $all_fee) {
                                    $num_fee = $all_fee;
                                    $all_fee = 0;
                                } else {
                                    $all_fee -= $num_fee;
                                }
                            } else {
                                $num_fee = 0;
                            }
                            $taker_base_saldo += $total - $num_fee;
                            $taker_trade_saldo -= $amount;
                            //$wallet_taker_base->balance += $total * (1 - $fee);
                            //$wallet_taker_trade->balance -= $amount;
                            $wallet_maker = Wallet::where('user_id', $trade->user_id)->where("coin_id", $market->trade_currency_id)->lockForUpdate()->first();
                            $wallet_maker->balance += $amount;
                            $trade->amount -= $amount;
                            $amount1 = $amount;
                            $amount = 0;
                            if (floatval(number_format($trade->amount, 8, '.', '')) <= 0.00000001) {
                                $trade->finished = true;
                            }
                            $trade->save();
                            //$wallet_taker_base->save();
                            //$wallet_taker_trade->save();
                            $wallet_maker->save();
                            /*//MARKET STUFF
                            $mh = MarketHistory::where("market_time", $rounded_date)->where('market_id', $market_id)->first();
                            $prevMarket = MarketHistory::where("market_time", $prev_date)->where('market_id', $market_id)->first();
                            if ($mh) {
                                if ($trade->price < $mh->low) {
                                    $mh->low = $trade->price;
                                } else if ($trade->price > $mh->high) {
                                    $mh->high = $trade->price;
                                }
                                if ($mh->low == 0) {
                                    $mh->low = $trade->price;
                                }
                                $mh->close = $trade->price;
                                $mh->trade_count++;
                                $mh->volume += $trade->amount;
                                $mh->volume_base += $trade->amount * $trade->price;
                                $mh->save();
                            } else {
                                $open = 0;
                                if ($prevMarket) {
                                    $open = $prevMarket->close;
                                } else {
                                    $open = $trade->price;
                                }
                                MarketHistory::create(['market_id' => $market_id, 'high' => $trade->price, 'low' => $trade->price, 'close' => $trade->price, 'market_time' => $rounded_date, 'open' => $open, 'volume' => $trade->amount, 'volume_base' => $trade->amount * $trade->price, 'trade_count' => 1]);
                            }


                            //MARKET STUFF*/
                            /*$ut = UserTrade::create([
                                "trade_id" => $trade->id,
                                "user_id_maker" => $trade->user_id,
                                "user_id_taker" => $user->id,
                                "type" => $trade->type,
                                "price" => $trade->price,
                                "amount" => $amount1,
                                "market_id" => $market_id,
                            ]);*/
                            $ut = new UserTrade;
                            $ut->trade_id = $trade->id;
                            $ut->user_id_maker = $trade->user_id;
                            $ut->user_id_taker = $user->id;
                            $ut->type = $trade->type;
                            $ut->price = $trade->price;
                            $ut->amount = $amount1;
                            $ut->market_id = $market_id;
                            $time = time();
                            list($usec, $sec) = explode(' ', microtime());
                            $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
                            $ut->created_at = $time;
                            $ut->updated_at = $time;
                            $points[] = new Point(
                                'trades',
                                floatval($ut->price),
                                [
                                    'trade_id' => $ut->trade_id,
                                    'type' => $ut->type,
                                    'market_id' => $ut->market_id,
                                    'user_id_taker' => $ut->user_id_taker,
                                    'user_id_maker' => $ut->user_id_maker],
                                ['amount' => floatval($ut->amount),
                                    'total' => floatval($ut->amount) * floatval($ut->price)
                                ],
                                $timestamp);

                            $events->push(new newUserTrade($ut));
                            $events->push(new TradeSuccessful($trade, $amount1));

                            //dump("Sold " . number_format($amount1, 8, '.', '') . " " . $trade_coin->name . " for " . number_format($total, 8, '.', '') . " " . $base_coin->name);
                            //dump("Fee:" . number_format($amount1 * ($fee), 8, '.', '') . " " . $trade_coin->name . " + " . number_format($total * ($fee), 8, '.', '') . " BTC");
                        }
                    }
                    $wallet_taker_trade->balance += $taker_trade_saldo;
                    $wallet_taker_base->balance += $taker_base_saldo;
                    $wallet_taker_base->save();
                    $wallet_taker_trade->save();
                }

                if (floatval(number_format($amount, 8, '.', '')) >= 0.00000001) {
                    $this->addTrade($amount, $price, $type, $market);
                }
                foreach ($events as $event) {
                    event($event);
                }
                try {
                    InfluxDB::writePoints($points, Database::PRECISION_MICROSECONDS);
                } catch (\Exception $e) {
                    report($e);
                };
                return json_encode(['status' => 'Trade successful']);
            }, 1);
        } catch (\Exception $exception) {
            report($exception);
            return json_encode(['status' => "Error"]);
        }
    }

    protected function addTrade($amount, $price, $type, $market)
    {
        return DB::transaction(function () use ($amount, $price, $type, $market) {

            $user_wallet = NULL;
            $user = Auth::user();
            $trade_coin = Coin::find($market->trade_currency_id);
            $fee = $trade_coin->trading_fee->fee;
            $plus = 1 + $fee;
            if ($type == "sell") {
                $user_wallet = Wallet::where('user_id', $user->id)->where('coin_id', $market->trade_currency_id)->lockForUpdate()->first();
                if ($user_wallet->balance == 0) {
                    return -1;
                }
                if ($user_wallet->balance < $amount) {
                    $amount = $user_wallet->balance;
                }


            } else
                if ($type == "buy") {
                    $user_wallet = Wallet::where('user_id', $user->id)->where('coin_id', $market->base_currency_id)->lockForUpdate()->first();
                    if ($user_wallet->balance == 0) {
                        return -1;
                    }
                    if ($user_wallet->balance < $amount * $price * $plus) {
                        $amount = floatval(number_format($user_wallet->balance / ($price * $plus)));
                    }

                }
            if ($t = Trade::create(["amount" => $amount, "price" => $price, "type" => $type, "market_id" => $market->id, "user_id" => $user->id, "fee" => $amount * $price * $fee])) {
                if ($type == 'sell') {
                    $user_wallet->balance -= $amount;
                    $user_wallet->save();
                    event(new newTrade($t));
                } else
                    if ($type == 'buy') {
                        $user_wallet->balance -= $amount * $price * $plus;
                        $user_wallet->save();
                        event(new newTrade($t));
                    }
                return json_encode(['status' => 'Trade successful']);
            } else {
                return -1;
            }
        }, 1);

    }
}
