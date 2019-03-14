<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Wallet;
use App\Coin;
use App\Deposit;
use App\Withdrawal;
use App\Market;
use App\Trade;
use Illuminate\Support\Facades\DB;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as JavaScript;
use Illuminate\Support\Facades\Cache;
use App\UserTrade;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class UserInfoController extends Controller
{
    public function balances()
    {
        $user = Auth::user();
        $wallets = Cache::remember("WalletUser" . $user->id, 60, function () use ($user) {
            return Wallet::where("user_id", $user->id)->get();
        });
        foreach ($wallets as $wallet) {
            $w_ids[] = $wallet->id;
        }
        foreach ($wallets as $wallet) {
            $c = Cache::remember("Coin" . $wallet->coin_id, 60, function () use ($wallet) {
                return Coin::where('id', $wallet->coin_id)->first();
            });
            $wallet->name = $c->name;
        }
        $deposits = Cache::remember("Deposit" . $user->id, 60, function () use ($w_ids) {
            return Deposit::whereIn('wallet_id', $w_ids)->orderBy('id', 'desc')->limit(10)->get();
        });
        $withdrawals = Cache::remember("Withdrawal" . $user->id, 60, function () use ($w_ids) {
            return Withdrawal::whereIn('wallet_id', $w_ids)->orderBy('id', 'desc')->limit(10)->get();
        });
        foreach ($deposits as $deposit) {
            $coin_id = Cache::remember("Wallet" . $deposit->wallet_id, 60, function () use ($deposit) {
                return Wallet::where('id', $deposit->wallet_id)->first();
            })->coin_id;
            $c = Cache::remember('Coin' . $coin_id, 60, function () use ($coin_id) {
                return Coin::where('id', $coin_id)->first();
            });
            $deposit->name = $c->name;
            $deposit->needed_confirmations = $c->needed_confirmations;
        }
        foreach ($withdrawals as $withdrawal) {
            $coin_id = Cache::remember("Wallet" . $withdrawal->wallet_id, 60, function () use ($withdrawal) {
                return Wallet::where("id", $withdrawal->wallet_id)->select("coin_id")->first();
            })->coin_id;
            $c = Cache::remember('Coin' . $coin_id, 60, function () use ($coin_id) {
                return Coin::where('id', $coin_id)->first();
            });
            $withdrawal->name = $c->name;
            $withdrawal->needed_confirmations = $c->needed_confirmations;
        }
        $trades = Cache::remember("TradeUser" . $user->id, 60, function () use ($user) {
            return Trade::where("user_id", $user->id)->select('type', 'amount', 'price', 'finished', 'market_id', 'updated_at', 'id')->orderByDesc('id')->limit(10)->get();
        });
        foreach ($trades as $trade) {
            $market = Cache::remember('Market' . $trade->market_id, 60, function () use ($trade) {
                return Market::where('id', $trade->market_id)->first();
            });
            $base_coin_name = Cache::remember("Coin" . $market->base_currency_id, 60, function () use ($market) {
                return Coin::where('id', $market->base_currency_id)->first();
            })->name;
            $trade_coin_name = Cache::remember("Coin" . $market->trade_currency_id, 60, function () use ($market) {
                return Coin::where('id', $market->trade_currency_id)->first();
            })->name;
            $trade->market = $base_coin_name . "/" . $trade_coin_name;
        }
        JavaScript::put([
            'wallets' => $wallets,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
            'trades' => $trades,
            'user_id' => $user->id,
            'notification_enabled' => auth()->user()->notification_enabled
        ]);
        return view('balances');
    }

    public function wallets()
    {
        $user = Auth::user();
        $wallets = Cache::remember("WalletUser" . $user->id, 60, function () use ($user) {
            return Wallet::where("user_id", $user->id)->get();
        });
        foreach ($wallets as $wallet) {
            $w_ids[] = $wallet->id;
        }
        foreach ($wallets as $wallet) {
            $c = Cache::remember("Coin" . $wallet->coin_id, 60, function () use ($wallet) {
                return Coin::where('id', $wallet->coin_id)->first();
            });
            $wallet->name = $c->name;
        }
        JavaScript::put([
            'wallets' => $wallets,
            'user_id' => $user->id,
            'notification_enabled' => auth()->user()->notification_enabled
        ]);
        return view('wallets');
    }

    public function depositHistory()
    {
        $user = Auth::user();
        $w_ids = array();
        $wallets = Cache::remember("WalletUser" . $user->id, 60, function () use ($user) {
            return Wallet::where("user_id", $user->id)->get();
        });
        foreach ($wallets as $wallet) {
            $w_ids[] = $wallet->id;
        }
        $deposits = Cache::remember("Deposit" . $user->id, 60, function () use ($w_ids) {
            return Deposit::whereIn('wallet_id', $w_ids)->orderBy('id', 'desc')->limit(10)->get();
        });
        foreach ($deposits as $deposit) {
            $coin_id = Cache::remember("Wallet" . $deposit->wallet_id, 60, function () use ($deposit) {
                return Wallet::where('id', $deposit->wallet_id)->first();
            })->coin_id;
            $c = Cache::remember('Coin' . $coin_id, 60, function () use ($coin_id) {
                return Coin::where('id', $coin_id)->first();
            });
            $deposit->name = $c->name;
            $deposit->needed_confirmations = $c->needed_confirmations;
        }
        $pages = Deposit::whereIn('wallet_id', $w_ids)->count();
        if ($pages % 10 == 0) {
            $pages = (int)($pages / 10);
        } else {
            $pages = (int)($pages / 10 + 1);
        }
        $pages--;
        JavaScript::put([
            'deposits' => $deposits,
            'user_id' => $user->id,
            'pages' => $pages,
            'notification_enabled' => auth()->user()->notification_enabled
        ]);
        return view('depositHistory');
    }

    public function depositHistoryPOST(Request $request)
    {
        $page = $request->post('page') ? $request->post('page') : 0;
        $user = Auth::user();
        $wallets = Cache::remember("WalletUser" . $user->id, 60, function () use ($user) {
            return Wallet::where("user_id", $user->id)->get();
        });
        foreach ($wallets as $wallet) {
            $w_ids[] = $wallet->id;
        }
        if ($page == 0) {
            $deposits = Cache::remember("Deposit" . $user->id, 60, function () use ($w_ids) {
                return Deposit::whereIn('wallet_id', $w_ids)->orderBy('id', 'desc')->limit(10)->get();
            });
        } else if ($page > 0) {
            $deposits = Deposit::whereIn('wallet_id', $w_ids)->orderBy('id', 'desc')->limit(10)->offset(10 * $page)->get();
        }
        foreach ($deposits as $deposit) {
            $coin_id = Cache::remember("Wallet" . $deposit->wallet_id, 60, function () use ($deposit) {
                return Wallet::where('id', $deposit->wallet_id)->first();
            })->coin_id;
            $c = Cache::remember('Coin' . $coin_id, 60, function () use ($coin_id) {
                return Coin::where('id', $coin_id)->first();
            });
            $deposit->name = $c->name;
            $deposit->needed_confirmations = $c->needed_confirmations;
        }
        return json_encode(['deposits' => $deposits]);
    }

    public function withdrawalHistory()
    {
        $w_ids = array();
        $user = Auth::user();
        $wallets = Cache::remember("WalletUser" . $user->id, 60, function () use ($user) {
            return Wallet::where("user_id", $user->id)->get();
        });
        foreach ($wallets as $wallet) {
            $w_ids[] = $wallet->id;
        }
        $withdrawals = Cache::remember("Withdrawal" . $user->id, 60, function () use ($w_ids) {
            return Withdrawal::whereIn('wallet_id', $w_ids)->orderBy('id', 'desc')->limit(10)->get();
        });
        foreach ($withdrawals as $withdrawal) {
            $coin_id = Cache::remember("Wallet" . $withdrawal->wallet_id, 60, function () use ($withdrawal) {
                return Wallet::where("id", $withdrawal->wallet_id)->select("coin_id")->first();
            })->coin_id;
            $c = Cache::remember('Coin' . $coin_id, 60, function () use ($coin_id) {
                return Coin::where('id', $coin_id)->first();
            });
            $withdrawal->name = $c->name;
            $withdrawal->needed_confirmations = $c->needed_confirmations;
        }
        $pages = Withdrawal::whereIn('wallet_id', $w_ids)->count();
        if ($pages % 10 == 0) {
            $pages = (int)($pages / 10);
        } else {
            $pages = (int)($pages / 10 + 1);
        }
        $pages--;

        JavaScript::put([
            'withdrawals' => $withdrawals,
            'pages' => $pages,
            'user_id' => $user->id,
            'notification_enabled' => auth()->user()->notification_enabled
        ]);
        return view('withdrawalHistory');
    }

    public function withdrawalHistoryPOST(Request $request)
    {
        $page = $request->post('page') ? $request->post('page') : 0;
        $user = Auth::user();
        $wallets = Cache::remember("WalletUser" . $user->id, 60, function () use ($user) {
            return Wallet::where("user_id", $user->id)->get();
        });
        foreach ($wallets as $wallet) {
            $w_ids[] = $wallet->id;
        }
        if ($page == 0) {
            $withdrawals = Cache::remember("Withdrawal" . $user->id, 60, function () use ($w_ids) {
                return Withdrawal::whereIn('wallet_id', $w_ids)->orderBy('id', 'desc')->limit(10)->get();
            });
        } else if ($page > 0) {
            $withdrawals = Withdrawal::whereIn('wallet_id', $w_ids)->orderBy('id', 'desc')->limit(10)->offset(10 * $page)->get();
        }

        foreach ($withdrawals as $withdrawal) {
            $coin_id = Cache::remember("Wallet" . $withdrawal->wallet_id, 60, function () use ($withdrawal) {
                return Wallet::where("id", $withdrawal->wallet_id)->select("coin_id")->first();
            })->coin_id;
            $c = Cache::remember('Coin' . $coin_id, 60, function () use ($coin_id) {
                return Coin::where('id', $coin_id)->first();
            });
            $withdrawal->name = $c->name;
            $withdrawal->needed_confirmations = $c->needed_confirmations;
        }
        return json_encode(['withdrawals' => $withdrawals]);
    }

    public function tradeHistory()
    {
        $user = Auth::user();
        $trades = InfluxDB::query("SELECT * from trades WHERE user_id_maker = '" . $user->id . "' OR user_id_taker = '" . $user->id . "' ORDER BY time DESC LIMIT 10")->getPoints();
        if ($trades != null) {

            for ($i = 0; $i < count($trades); $i++) {
                unset($trades[$i]['user_id_maker']);
                unset($trades[$i]['user_id_taker']);
                $market = Cache::remember('Market' . $trades[$i]['market_id'], 60, function () use ($trades, $i) {
                    return Market::where('id', $trades[$i]['market_id'])->first();
                });
                $base_coin_name = Cache::remember("Coin" . $market->base_currency_id, 60, function () use ($market) {
                    return Coin::where('id', $market->base_currency_id)->first();
                })->name;
                $trade_coin_name = Cache::remember("Coin" . $market->trade_currency_id, 60, function () use ($market) {
                    return Coin::where('id', $market->trade_currency_id)->first();
                })->name;
                $trades[$i]['market'] = $base_coin_name . "/" . $trade_coin_name;
                $trades[$i]['created_at'] = Carbon::createFromTimeString($trades[$i]['time'])->tz($user->timezone)->toDateTimeString();
                $trades[$i]['price'] = $trades[$i]['value'];
                unset($trades[$i]['time']);
                unset($trades[$i]['value']);
                unset($trades[$i]['trade_id']);

            }
        }

        $pages = InfluxDB::query("SELECT COUNT(value) from trades WHERE user_id_maker = '2' OR user_id_taker = '2'")->getPoints()/*[0]['count']*/;
        if ($pages!=null)
        {
            $pages = $pages[0]['count'];
        }
        else
        {
            $pages=0;
        }
        if ($pages % 10 == 0) {
            $pages = (int)($pages / 10);
        } else {
            $pages = (int)($pages / 10 + 1);
        }
        $pages--;
        JavaScript::put([
            'trades' => $trades,
            'user_id' => $user->id,
            'pages' => $pages,
            'notification_enabled' => auth()->user()->notification_enabled
        ]);
        return view('tradeHistory');
    }

    public function tradeHistoryPOST(Request $request)
    {
        $page = $request->post('page') ? $request->post('page') : 0;
        $user = Auth::user();
        $trades = InfluxDB::query("SELECT * from trades WHERE user_id_maker = '" . $user->id . "' OR user_id_taker = '" . $user->id . "' ORDER BY time DESC LIMIT 10 OFFSET " . ($page * 10))->getPoints();
        if ($trades != null) {

            for ($i = 0; $i < count($trades); $i++) {
                unset($trades[$i]['user_id_maker']);
                unset($trades[$i]['user_id_taker']);
                $market = Cache::remember('Market' . $trades[$i]['market_id'], 60, function () use ($trades, $i) {
                    return Market::where('id', $trades[$i]['market_id'])->first();
                });
                $base_coin_name = Cache::remember("Coin" . $market->base_currency_id, 60, function () use ($market) {
                    return Coin::where('id', $market->base_currency_id)->first();
                })->name;
                $trade_coin_name = Cache::remember("Coin" . $market->trade_currency_id, 60, function () use ($market) {
                    return Coin::where('id', $market->trade_currency_id)->first();
                })->name;
                $trades[$i]['market'] = $base_coin_name . "/" . $trade_coin_name;
                $trades[$i]['created_at'] = Carbon::createFromTimeString($trades[$i]['time'])->tz($user->timezone)->toDateTimeString();
                $trades[$i]['price'] = $trades[$i]['value'];
                unset($trades[$i]['time']);
                unset($trades[$i]['value']);
                unset($trades[$i]['trade_id']);

            }
        }
        return json_encode(["trades" => $trades]);
    }

    public function trades(Request $request)
    {
        $user = auth()->user();
        $balance = 0;
        $trades = Trade::where('user_id', $user->id)->where('finished', 0)->orderByDesc('id')->limit(10)->get();
        foreach ($trades as $trade) {
            $m = Market::where('id', $trade->market_id)->first();
            $base_coin = Coin::where('id', $m->base_currency_id)->first()->name;
            $trade_coin = Coin::where('id', $m->trade_currency_id)->first()->name;
            $trade->market = $base_coin . "/" . $trade_coin;

            $q=InfluxDB::query("SELECT sum(amount) from trades where trade_id='".$trade->id."'")->getPoints();
            if ($q!=null)
            {
                $trade->amount_traded = floatval(number_format($q[0]['sum'],8,'.',''));
                $trade->amount+=$trade->amount_traded;
            }
            else
            {
                $trade->amount_traded=0;
            }
        }
        $pages = Trade::where('user_id', $user->id)->where('finished', 0)->count();
        if ($pages % 10 == 0) {
            $pages = (int)($pages / 10);
        } else {
            $pages = (int)($pages / 10 + 1);
        }
        $pages--;
        JavaScript::put([
            'balance' => $balance,
            'user_id' => $user->id,
            'trades' => $trades,
            'pages' => $pages,
            'is_notifications' => $user->notification_enabled
        ]);
        return view('trades');
    }

    public function tradesPOST(Request $request)
    {
        $request->validate([
           'page'=>'required'
        ]);
        $user = auth()->user();
        $page = $request->post('page');
        $trades = Trade::where('user_id', $user->id)->where('finished', 0)->orderByDesc('id')->limit(10)->offset($page * 10)->get();
        foreach ($trades as $trade) {
            $m = Market::where('id', $trade->market_id)->first();
            $base_coin = Coin::where('id', $m->base_currency_id)->first()->name;
            $trade_coin = Coin::where('id', $m->trade_currency_id)->first()->name;
            $trade->market = $base_coin . "/" . $trade_coin;
            $trade->amount_traded = UserTrade::where('trade_id', $trade->id)->sum('amount');
        }
        return json_encode(['trades' => $trades]);
    }

    public function settingsPOST(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $is_notifications = $request->post('is_notifications');
            $selected_area = $request->post('selected_area');
            $selected_city = $request->post('selected_city');
            if (!isset($is_notifications) || !isset($selected_area) || !isset($selected_city)) {
                return -1;
            }
            $user = auth()->user();
            if ($selected_area != "UTC") {
                $timezone = $selected_area . "/" . $selected_city;
                if (!in_array($timezone, timezone_identifiers_list())) {
                    return -1;
                }
                $user->timezone = $timezone;
            } else {
                $user->timezone = "UTC";
            }
            $user->notification_enabled = $is_notifications;
            $user->save();
            return 1;
        });
    }

    public function settings()
    {
        $user = auth()->user();
        $timezone = $user->timezone;
        $t = explode("/", $timezone);
        $time_area = $t[0];
        if (isset($t[1])) {
            $time_city = $t[1];
        } else {
            $time_city = 0;
        }
        JavaScript::put([
            'user_id' => $user->id,
            'selected_area' => $time_area,
            'selected_city' => $time_city,
            'code'=>$user->anticode->code,
            'is_notifications' => $user->notification_enabled
        ]);
        return view('settings');
    }
}
