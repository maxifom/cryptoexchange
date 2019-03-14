<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

use App\Events\NewTrade as newTrade;
use Denpa\Bitcoin\Client as BitcoinClient;
use App\Coin;
use App\Market;
use TrayLabs\InfluxDB\Facades\InfluxDB;
use App\Http\Resources\Coin as CoinResource;
use App\Http\Resources\MarketResource;

Route::group(['middleware' => ['api.custom']], function () {

    Route::post('/getCoins', function () {
        $coins = Coin::where('status', 'confirmed')->get();
        return CoinResource::collection($coins);
    });
    Route::post('/getMarkets', function () {
        $markets = Market::where('confirmed', 1)->get();
        for ($i = 0; $i < count($markets); $i++) {
            if (!$markets[$i]->confirmed)
            {
                unset($markets[$i]);
                continue;
            }
            $count_trades = 0;
            $c = InfluxDB::query("SELECT COUNT(value) FROM trades WHERE market_id='" . $markets[$i]->id . "' AND time>=now()-1d")->getPoints();
            $last = InfluxDB::query("SELECT LAST(value) FROM trades WHERE market_id='" . $markets[$i]->id . "'")->getPoints();
            if ($last)
            {
                $markets[$i]->last_price = number_format($last[0]['last'],8,'.','');
            }
            else
            {
                $markets[$i]->last_price = null;
            }
            if ($c != null) {
                $markets[$i]->trade_count_24hrs = $c[0]['count'] ?: 0;
                $low = InfluxDB::query("SELECT MIN(value) FROM trades WHERE market_id='" . $markets[$i]->id . "' AND time>=now()-1d")->getPoints();
                $high = InfluxDB::query("SELECT MAX(value) FROM trades WHERE market_id='" . $markets[$i]->id . "' AND time>=now()-1d")->getPoints();
                $close = InfluxDB::query("SELECT LAST(value) FROM trades WHERE market_id='" . $markets[$i]->id . "' AND time>=now()-1d")->getPoints();
                $open = InfluxDB::query("SELECT FIRST(value) FROM trades WHERE market_id='" . $markets[$i]->id . "' AND time>=now()-1d")->getPoints();
                $volume_base = InfluxDB::query("SELECT SUM(total) FROM trades WHERE market_id='" . $markets[$i]->id . "' AND time>=now()-1d")->getPoints();
                $volume_trade = InfluxDB::query("SELECT SUM(amount) FROM trades WHERE market_id='" . $markets[$i]->id . "' AND time>=now()-1d")->getPoints();

                $markets[$i]->low = $low[0]['min'];
                $markets[$i]->close = $close[0]['last'];
                $markets[$i]->high = $high[0]['max'];
                $markets[$i]->open = $open[0]['first'];
                $markets[$i]->volume_base = floatval(number_format($volume_base[0]['sum'], 8, '.', ''));
                $markets[$i]->volume_trade = floatval(number_format($volume_trade[0]['sum'], 8, '.', ''));
            }
            else {
                $markets[$i]->low = 0;
                $markets[$i]->close = 0;
                $markets[$i]->high = 0;
                $markets[$i]->open = 0;
                $markets[$i]->volume_base = 0;
                $markets[$i]->volume_trade = 0;
            }
            $markets[$i]->trade_count_24hrs = $count_trades;
            $base_coin_name = Coin::where("id", $markets[$i]->base_currency_id)->first()->name;
            $trade_coin_name = Coin::where("id", $markets[$i]->trade_currency_id)->first()->name;
            $markets[$i]->name = $base_coin_name . "/" . $trade_coin_name;
            $this->base_coin_name = $base_coin_name;
            $this->trade_coin_name = $trade_coin_name;
        }
        return MarketResource::collection($markets);
    });

    Route::post('/getMarket',function (Request $request)
    {
        try
        {
            $market_id = $request->post('market_id');
            $market_name = $request->post('market_name');
            $market=null;
            if (!$market_id && !$market_name)
            {
                return ['data' => null, 'error' => true];
            }
            if ($market_id)
            {
                $market = Market::find($market_id);
            }
            else if ($market_name)
            {
                $market_name = explode("/", $market_name);
                if (count($market_name) == 2) {
                    $base_coin = Coin::where('name', $market_name[0])->first();
                    $trade_coin = Coin::where('name', $market_name[1])->first();
                    if ($base_coin != null || $trade_coin != null) {
                        $market = Market::where('base_currency_id', $base_coin->id)->where('trade_currency_id', $trade_coin->id)->first();
                    }
                }
            }
            if (!$market)
            {
                return ['data' => null, 'error' => true];
            }
            $count_trades = 0;
            $c = InfluxDB::query("SELECT COUNT(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
            $last = InfluxDB::query("SELECT LAST(value) FROM trades WHERE market_id='" . $market->id . "'")->getPoints();
            if ($last)
            {
                $market->last_price = number_format($last[0]['last'],8,'.','');
            }
            else
            {
                $market->last_price = null;
            }
            if ($c != null) {
                $market->trade_count_24hrs = $c[0]['count'] ?: 0;
                $low = InfluxDB::query("SELECT MIN(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $high = InfluxDB::query("SELECT MAX(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $close = InfluxDB::query("SELECT LAST(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $open = InfluxDB::query("SELECT FIRST(value) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $volume_base = InfluxDB::query("SELECT SUM(total) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();
                $volume_trade = InfluxDB::query("SELECT SUM(amount) FROM trades WHERE market_id='" . $market->id . "' AND time>=now()-1d")->getPoints();

                $market->low = $low[0]['min'];
                $market->close = $close[0]['last'];
                $market->high = $high[0]['max'];
                $market->open = $open[0]['first'];
                $market->volume_base = floatval(number_format($volume_base[0]['sum'], 8, '.', ''));
                $market->volume_trade = floatval(number_format($volume_trade[0]['sum'], 8, '.', ''));
            }
            else {
                $market->low = 0;
                $market->close = 0;
                $market->high = 0;
                $market->open = 0;
                $market->volume_base = 0;
                $market->volume_trade = 0;
            }
            $market->trade_count_24hrs = $count_trades;
            $base_coin_name = Coin::where("id", $market->base_currency_id)->first()->name;
            $trade_coin_name = Coin::where("id", $market->trade_currency_id)->first()->name;
            $market->name = $base_coin_name . "/" . $trade_coin_name;
            $this->base_coin_name = $base_coin_name;
            $this->trade_coin_name = $trade_coin_name;
            return (new MarketResource($market))->additional(['error' => false]);
        }
        catch (\Exception $exception)
        {
            report($exception);
            return ['data' => null, 'error' => true];
        }
    });

    Route::post('/getLastMarketTrades', function (Request $request) {
        try {
            $token = $request->input('token');
            $name = $request->input('market_name');
            $id = null;
            if ($name != null) {
                $name = explode("/", $name);
                if (count($name) == 2) {
                    $base_coin = Coin::where('name', $name[0])->first();
                    $trade_coin = Coin::where('name', $name[1])->first();
                    if ($base_coin != null || $trade_coin != null) {
                        $id = Market::where('base_currency_id', $base_coin->id)->where('trade_currency_id', $trade_coin->id)->first()->id;
                    }
                }

            }
            if ($id === null)
                $id = $request->input('market_id');
            if ($id != null) {

                $trades = InfluxDB::query("SELECT value,amount,time,type from trades where market_id = '" . $id . "' ORDER BY time DESC LIMIT 200")->getPoints();
                if ($trades == null) {
                    return ['data' => null, 'error' => true];
                }
                $api = \App\ApiEntry::where('token', $token)->first();
                $user = \App\User::find($api->user_id);
                for ($i = 0; $i < count($trades); $i++) {
                    $trades[$i]['time'] = \Carbon\Carbon::createFromTimeString($trades[$i]['time'])->tz($user->timezone)->format("Y-m-d H:i:s");
                    $trades[$i]['price'] = $trades[$i]['value'];
                    unset($trades[$i]['value']);
                }
                return ['data' => $trades, 'error' => false, 'timezone' => $user->timezone];

            } else {
                return ['data' => null, 'error' => true];
            }
        } catch (Exception $exception) {
            report($exception);
            return ['data' => null, 'error' => true];
        }

    });
    Route::post('/getWallets', function (Request $request) {
        try {

            $token = $request->input('token');
            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->wallet === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $wallets = $user->wallets;
            return \App\Http\Resources\WalletResource::collection($wallets)->additional(['error' => false]);
        } catch (Exception $exception) {
            return ['data' => null, 'error' => true];
        }
    });
    Route::post('/getDepositHistory', function (Request $request) {
        try {
            $token = $request->input('token');
            $page = $request->input('page');
            if ($page === null) {
                $page = 1;
            } else {
                $page = (int)$page;
            }

            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->wallet === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $wallets_ids = \App\Wallet::where('user_id', $user->id)->select('id')->get()->pluck('id');
            $pages = \App\Deposit::whereIn('wallet_id', $wallets_ids)->count();
            if ($pages % 100 == 0) {
                $pages = (int)($pages / 100);
            } else {
                $pages = (int)($pages / 100) + 1;
            }
            if ($page > $pages) {
                $page = $pages;
            }
            if ($page < 1) {
                $page = 1;
            }
            $deposits = \App\Deposit::whereIn('wallet_id', $wallets_ids)->orderByDesc('id')->limit(100)->offset(($page - 1) * 100)->get();
            foreach ($deposits as $deposit) {
                $deposit->tx_time = \Carbon\Carbon::createFromTimeString($deposit->tx_time)->tz($user->timezone)->format("Y-m-d H:i:s");
            }
            return \App\Http\Resources\DepositResource::collection($deposits)->additional(['pages' => $pages, 'page' => $page, 'timezone' => $user->timezone, 'error' => false]);
        } catch (Exception $exception) {
            report($exception);
            return ['data' => null, 'error' => true];
        }
    });
    Route::post('/getWithdrawalHistory', function (Request $request) {
        try {

            $token = $request->input('token');
            $page = $request->input('page');
            if ($page === null) {
                $page = 1;
            } else {
                $page = (int)$page;
            }
            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->wallet === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $wallets_ids = \App\Wallet::where('user_id', $user->id)->select('id')->get()->pluck('id');
            $pages = \App\Withdrawal::whereIn('wallet_id', $wallets_ids)->count();
            if ($pages % 100 == 0) {
                $pages = (int)($pages / 100);
            } else {
                $pages = (int)($pages / 100) + 1;
            }
            if ($page > $pages) {
                $page = $pages;
            }
            if ($page < 1) {
                $page = 1;
            }
            $withdrawals = \App\Withdrawal::whereIn('wallet_id', $wallets_ids)->orderByDesc('id')->limit(100)->offset(($page - 1) * 100)->get();
            foreach ($withdrawals as $w) {
                $time = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $w->getOriginal('updated_at'))->tz($user->timezone)->format("Y-m-d H:i:s");
                $w->updated_at = $time;
            }
            return \App\Http\Resources\WithdrawalResource::collection($withdrawals)->additional(['pages' => $pages, 'page' => $page, 'timezone' => $user->timezone, 'error' => false]);
        } catch (Exception $exception) {
            report($exception);
            return ['data' => null, 'error' => true];

        }
    });
    Route::post('/getTradeHistory', function (Request $request) {
        try {

            $token = $request->input('token');
            $page = $request->input('page');
            if ($page === null) {
                $page = 1;
            } else {
                $page = (int)$page;
            }
            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->trade === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $pages = InfluxDB::query("SELECT COUNT(value) FROM trades WHERE user_id_maker='" . $user->id . "' OR user_id_taker='" . $user->id . "'")->getPoints();
            $pages = $pages[0]['count'];
            if ($pages % 100 == 0) {
                $pages = (int)($pages / 100);
            } else {
                $pages = (int)($pages / 100) + 1;
            }
            if ($page > $pages) {
                $page = $pages;
            }
            if ($page < 1) {
                $page = 1;
            }
            $trades = InfluxDB::query("SELECT * FROM trades WHERE user_id_maker='" . $user->id . "' OR user_id_taker='" . $user->id . "' ORDER BY time DESC LIMIT 100 OFFSET " . (($page - 1) * 100))->getPoints();
            for ($i = 0; $i < count($trades); $i++) {
                unset($trades[$i]['user_id_taker']);
                unset($trades[$i]['user_id_maker']);
                unset($trades[$i]['trade_id']);
                $trades[$i]['price'] = $trades[$i]['value'];
                unset($trades[$i]['value']);
                unset($trades[$i]['total']);
                $trades[$i]['time'] = \Carbon\Carbon::createFromTimeString($trades[$i]['time'])->tz($user->timezone)->format("Y-m-d H:i:s");
            }
            return ['data' => $trades, 'pages' => $pages, 'page' => $page, 'timezone' => $user->timezone, 'error' => false];
        } catch (Exception $exception) {
            report($exception);

            return ['data' => null, 'error' => true];
        }
    });
    Route::post('/getOpenTrades', function (Request $request) {
        try {
            $token = $request->input('token');
            $page = $request->input('page');
            if ($page === null) {
                $page = 1;
            } else {
                $page = (int)$page;
            }
            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->trade === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $pages = \App\Trade::where('user_id', $user->id)->count();
            if ($pages % 100 == 0) {
                $pages = (int)($pages / 100);
            } else {
                $pages = (int)($pages / 100) + 1;
            }
            if ($page > $pages) {
                $page = $pages;
            }
            if ($page < 1) {
                $page = 1;
            }
            $trades = \App\Trade::where('user_id', $user->id)->where('finished', 0)->orderByDesc('id')->limit(100)->offset(($page - 1) * 100)->get();
            for ($i = 0; $i < count($trades); $i++) {

                $time = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $trades[$i]->getOriginal('updated_at'))->tz($user->timezone)->format("Y-m-d H:i:s");
                $trades[$i]->updated_at = $time;
                $q = InfluxDB::query("SELECT SUM(amount) FROM trades WHERE trade_id = '" . $trades[$i]->id . "'")->getPoints();
                if ($q != null) {
                    $q = $q[0]['sum'];
                } else {
                    $q = 0;
                }
                $trades[$i]->fulfilled_amount = $q;
            }
            return \App\Http\Resources\TradeResource::collection($trades)->additional(['pages' => $pages, 'page' => $page, 'timezone' => $user->timezone, 'error' => false]);
        } catch (Exception $exception) {
            report($exception);

            return ['data' => null, 'error' => true];
        }
    });
    Route::post('/trade', function (Request $request) {
        try {

            $token = $request->input('token');
            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->trade === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $price = $request->input('price');
            $amount = $request->input('amount');
            $market_id = $request->input('market_id');
            $market_name = $request->input('market_name');
            $type = $request->input('type');
            $market = null;
            if (!$price || !$amount || (!$market_id && !$market_name) && !$type && $price >= 0.00000001) {
                return ['data' => null, 'error' => true];
            }
            if ($market_id != null) {
                $market = Market::find($market_id);
            }
            if ($market == null && $market_name != null) {
                $name = explode("/", $market_name);
                if (count($name) == 2) {
                    $base_id = Coin::where('name', $name[0])->first();
                    $trade_id = Coin::where('name', $name[1])->first();
                    if (!$base_id && !$trade_id) {
                        $base_id = $base_id->id;
                        $trade_id = $trade_id->id;
                    }
                } else {
                    return ['data' => null, 'error' => true];
                }
                $market = Market::where('base_currency_id', $base_id)->where('trade_currency_id', $trade_id)->first();
            }
            if ($market != null) {
                return makeTrade($price, $amount, $market, $type, $user);
            } else {
                return ['data' => null, 'error' => true];
            }
        } catch (Exception $exception) {
            report($exception);

            return ['data' => null, 'error' => true];
        }
    });
    Route::post('/withdraw', function (Request $request) {
        try {

            $token = $request->input('token');
            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->wallet === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $coin_id = $request->input('coin_id');
            $coin_name = $request->input('coin_name');
            $value = $request->input('value');
            $address = $request->input('address');
            if (!$address || !$value || (!$coin_name && !$coin_id)) {
                return ['data' => null, 'error' => true];
            }
            $coin = null;
            if ($coin_id != null) {
                $coin = Coin::find($coin_id);
            }
            if ($coin == null && $coin_name != null) {
                $coin = Coin::where('name', $coin_name)->first();
            }
            if ($coin == null) {
                return ['data' => null, 'error' => true];
            } else {
                return withdraw($coin, $user, $value, $address);
            }
        } catch (Exception $exception) {
            report($exception);

            return ['data' => null, 'error' => true];
        }
    });
    Route::post('/closeTrade', function (Request $request) {
        try {
            $token = $request->input('token');
            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->trade === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $trade_id = $request->input('trade_id');
            $trade = \App\Trade::find($trade_id);
            if ($trade == null || !$trade_id) {
                return ['data' => null, 'error' => true];
            } else {
                return closeTrade($user, $trade);
            }
        } catch (Exception $exception) {
            report($exception);

            return ['data' => null, 'error' => true];
        }
    });
    Route::post('/getMarketTrades', function (Request $request) {
        try {

            $token = $request->input('token');
            $api = \App\ApiEntry::where('token', $token)->first();
            $market_id = $request->input('market_id');
            $market_name = $request->input('market_name');
            $type = $request->input('type');
            if (!$type || (!$market_id && !$market_name)) {
                return ['data' => null, 'error' => true];
            }
            $market = null;
            if ($market_id != null) {
                $market = Market::find($market_id);
            }
            if ($market == null && $market_name != null) {
                $name = explode("/", $market_name);
                if (count($name) == 2) {
                    $base_id = Coin::where('name', $name[0])->first()->id;
                    $trade_id = Coin::where('name', $name[1])->first()->id;
                } else {
                    return ['data' => null, 'error' => true];
                }
                $market = Market::where('base_currency_id', $base_id)->where('trade_currency_id', $trade_id)->first();
            }
            if ($market == null) {
                return ['data' => null, 'error' => true];
            }

            $base_name = Coin::where('id', $market->base_currency_id)->first()->name;
            $trade_name = Coin::where('id', $market->trade_currency_id)->first()->name;
            $market_name = $base_name . "/" . $trade_name;
            if ($type == 'buy') {
                $buy_trades = \Illuminate\Support\Facades\Cache::remember("TradeMarketBuy" . $market->id, 60, function () use ($market) {
                    return \App\Trade::where('market_id', $market->id)->where("type", "buy")->where('finished', false)->orderByDesc("price")->get();
                });
                $buy_trades_final = new \Illuminate\Support\Collection();
                $buy_trades = $buy_trades->groupBy('price');
                $i = 0;
                foreach ($buy_trades as $price) {
                    if ($i == 50) {
                        break;
                    }
                    $buy_trades_final->push(["amount" => $price->sum('amount'), "price" => $price[0]->price, "market_id" => $price[0]->market_id, 'market_name' => $market_name, 'type' => 'buy']);
                    $i++;
                }
            }
            if ($type == 'sell') {
                $sell_trades = \Illuminate\Support\Facades\Cache::remember("TradeMarketSell" . $market->id, 60, function () use ($market) {
                    return \App\Trade::where('market_id', $market->id)->where("type", "sell")->where('finished', false)->orderBy("price")->get();
                });
                $sell_trades = $sell_trades->groupBy('price');
                $sell_trades_final = new \Illuminate\Support\Collection();
                $i = 0;
                foreach ($sell_trades as $price) {
                    if ($i == 50) {
                        break;
                    }
                    $sell_trades_final->push(["amount" => $price->sum('amount'), "price" => $price[0]->price, "market_id" => $price[0]->market_id, 'market_name' => $market_name, 'type' => 'sell']);
                    $i++;
                }
            }

            $trades = null;
            if ($type == 'buy') {
                $trades = $buy_trades_final;
            } else if ($type == 'sell') {
                $trades = $sell_trades_final;
            }
            return ['data' => $trades, 'error' => false];
        } catch (Exception $exception) {
            report($exception);

            return ['data' => null, 'error' => true];
        }
    });
    Route::post('/createWallet', function (Request $request) {
        try {

            $token = $request->input('token');
            $api = \App\ApiEntry::where('token', $token)->first();
            if ($api->wallet === 0) {
                return ['data' => null, 'error' => true];
            }
            $user = \App\User::find($api->user_id);
            $coin_id = $request->input('coin_id');
            $coin_name = $request->input('coin_name');
            if (!$coin_name && !$coin_id) {
                return ['data' => null, 'error' => true];
            }
            $coin = null;
            if ($coin_id != null) {
                $coin = Coin::find($coin_id);
            }
            if ($coin == null && $coin_name != null) {
                $coin = Coin::where('name', $coin_name)->first();
            }
            if ($coin == null) {
                return ['data' => null, 'error' => true];
            } else {
                return createWalletAddress($user, $coin);
            }
        } catch (Exception $exception) {
            report($exception);

            return ['data' => null, 'error' => true];

        }
    });
});
function withdraw($coin, $user, $value, $address)
{
    return \Illuminate\Support\Facades\DB::transaction(function () use ($coin, $user, $value, $address) {

        if ($coin->status != 'confirmed' && $user->admin === 0) {
            return ['data' => null, 'error' => true];
        }
        $fee = \Illuminate\Support\Facades\Cache::remember('Fee' . $coin->id, 60, function () use ($coin) {
            return \App\Fee::where('coin_id', $coin->id)->first();
        });
        $wallet = \App\Wallet::where('coin_id', $coin->id)->where('user_id', $user->id)->lockForUpdate()->first();
        //$value = $_POST['value'];
        if (is_numeric($value))
            $value = number_format($_POST['value'], 8, '.', '');
        //$address = $_POST['address'];
        $validator = \Illuminate\Support\Facades\Validator::make(["value" => $value, "address" => $address], [
            'value' => ["bail", "required", new \App\Rules\isNumeric(), new \App\Rules\NumericLessThan($fee->fee * 1.5), new \App\Rules\NumericMoreThan($wallet->balance)],
            'address' => ["bail", "required", new \App\Rules\ValidAddress($coin->name)]
        ]);

        if ($validator->fails()) {
            return ['data' => null, 'error' => true];
        }
        $w = \App\Withdrawal::create(["wallet_id" => $wallet->id, 'value' => number_format($value - $fee->fee, 8, '.', ''), 'address' => $address, 'status' => 'approved']);
        $wallet->balance -= floatval(number_format($value, 8, '.', ''));
        $wallet->save();
        \App\Jobs\ProcessWithdrawal::dispatch($w->id);
        return ['data' => 'success', 'error' => false];
    }, 1);
}

function createWalletAddress($user, $coin)
{
    return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $coin) {
        if ($coin->status != 'confirmed' && $user->admin === 0) {
            return ['data' => null, 'error' => true];
        }
        $walletFind = \Illuminate\Support\Facades\Cache::remember("WalletUser" . $user->id . "Coin" . $coin->id, 60, function () use ($coin, $user) {
            return \App\Wallet::where('coin_id', $coin->id)->where('user_id', 2)->first();
        });
        if ($walletFind != NULL) {
            if ($walletFind->address != NULL) {
                return ['data' => null, 'error' => true];
            }
            $bitcoind = new BitcoinClient('http://' . $coin['user'] . ':' . $coin['pass'] . '@localhost:' . $coin['port'] . '/');
            $walletFind->address = $bitcoind->getnewaddress()->get();
            $walletFind->save();
            return ["data" => ["address" => $walletFind->address, 'coin_name' => $coin->name, 'coin_id' => $coin->id], 'error' => false];
        } else {
            return ['data' => null, 'error' => true];
        }
    }, 1);
}

function closeTrade($user, $trade)
{
    return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $trade) {
        if ($user->id === $trade->user_id && $trade->finished === 0) {
            if ($trade->type == 'sell') {
                $trade_coin_id = \Illuminate\Support\Facades\Cache::remember("Market" . $trade->market_id, 60, function () use ($trade) {
                    return Market::where('id', $trade->market_id)->first();
                })->trade_currency_id;
                $wallet = \Illuminate\Support\Facades\Cache::remember("WalletUser" . $user->id . "Coin" . $trade_coin_id, 60, function () use ($trade_coin_id, $user) {
                    return \App\Wallet::where('coin_id', $trade_coin_id)->where('user_id', $user->id)->first();
                });
                $wallet->balance += $trade->amount;
                $wallet->save();
            } else {
                if ($trade->type == 'buy') {
                    $base_coin_id = \Illuminate\Support\Facades\Cache::remember("Market" . $trade->market_id, 60, function () use ($trade) {
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
            event(new \App\Events\TradeDeleted($trade));
            return ['data' => 'success', 'error' => true];
        } else {
            return ['data' => null, 'error' => true];
        }
    }, 1);
}


function makeTrade($price, $amount, $market, $type, $user)
{
    /* $request->validate([
         'price' => 'required',
         'amount' => 'required',
         'market_id' => 'required',
         'type' => 'required'
     ]);*/
    return \Illuminate\Support\Facades\DB::transaction(function () use ($price, $amount, $market, $type, $user) {
        $fulfilled_amount = 0;
        $created_trade = null;
        $points = [];
        $events = new \Illuminate\Support\Collection();
        //$round_numerator = 60 * 30;
        //$rounded_time = round(time() / $round_numerator) * $round_numerator;
        /*$price = $request->post('price');
        $amount = $request->post('amount');*/
        //$market_id = $request->post('market_id');
        //$type = $request->post('type');
        //$user = Auth::user();
        //$market = Market::where('id', $market_id)->first();
        $trade_coin = Coin::where('id', $market->trade_currency_id)->first();
        if (!$market->confirmed) {
            return -1;
        }
        $fee = $trade_coin->trading_fee->fee;
        $minimal_trade = 300e-8;
        if ($price * $amount < $minimal_trade) {
            return ['data' => null, 'error' => true];

        }
        //$fee = 0.2 / 100;
        $wallet_taker_base = \App\Wallet::where('coin_id', $market->base_currency_id)->where('user_id', $user->id)->lockForUpdate()->first();
        $wallet_taker_trade = \App\Wallet::where('coin_id', $market->trade_currency_id)->where('user_id', $user->id)->lockForUpdate()->first();
        if ($type == 'buy') {                               //buy SCORE for BTC

            if ($wallet_taker_base->balance < $amount * $price * $fee && $wallet_taker_base->balance >= 0.00000001) {
                return ['data' => null, 'error' => true];
                //$amount = floatval(number_format($wallet_taker_base->balance / $price, 8, '.', ''));
            }

            $trades = \App\Trade::where('market_id',$market->id)->where("price", "<=", $price)->where('type', 'sell')->where('finished', false)->where("user_id", "<>", $user->id)->orderBy('price')->orderBy('id')->lockForUpdate()->get();
            if ($trades->isEmpty()) {

                $created_trade = addTrade($amount, $price, $type, $market, $user);
                return ['data' => ['fulfilled_amount' => $fulfilled_amount, 'created_trade' => $created_trade->toApi()], 'error' => false];
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
                    break;
                }
            }
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
                    $wallet_maker = \App\Wallet::where('user_id', $trade->user_id)->where("coin_id", $market->base_currency_id)->lockForUpdate()->first();
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
                    $wallet_maker->save();
                    $ut = new \App\UserTrade();
                    $ut->trade_id = $trade->id;
                    $ut->user_id_maker = $trade->user_id;
                    $ut->user_id_taker = $user->id;
                    $ut->type = $trade->type;
                    $ut->price = $trade->price;
                    $ut->amount = $trade->amount;
                    $fulfilled_amount += $ut->amount;
                    $ut->market_id = $market->id;
                    $time = time();
                    list($usec, $sec) = explode(' ', microtime());
                    $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
                    $ut->created_at = $time;
                    $ut->updated_at = $time;
                    $points[] = new \InfluxDB\Point(
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
                    $events->push(new \App\Events\newUserTrade($ut));
                    $events->push(new \App\Events\TradeSuccessful($trade, $trade->amount));


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
                    $wallet_maker = \App\Wallet::where('user_id', $trade->user_id)->where("coin_id", $market->base_currency_id)->lockForUpdate()->first();
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
                    $taker_trade_saldo += $amount;
                    $trade->amount -= $amount;
                    $amount1 = $amount;
                    $amount = 0;
                    if (floatval(number_format($trade->amount, 8, '.', '')) <= 0.00000001) {
                        $trade->finished = true;
                    }
                    $trade->save();
                    $wallet_maker->save();
                    $ut = new \App\UserTrade();
                    $ut->trade_id = $trade->id;
                    $ut->user_id_maker = $trade->user_id;
                    $ut->user_id_taker = $user->id;
                    $ut->type = $trade->type;
                    $ut->price = $trade->price;
                    $ut->amount = $amount1;
                    $fulfilled_amount += $amount1;
                    $ut->market_id = $market->id;
                    $time = time();
                    list($usec, $sec) = explode(' ', microtime());
                    $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
                    $ut->created_at = $time;
                    $ut->updated_at = $time;
                    $points[] = new \InfluxDB\Point(
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
                    $events->push(new \App\Events\newUserTrade($ut));
                    $events->push(new \App\Events\TradeSuccessful($trade, $amount1));
                }
            }
            $wallet_taker_trade->balance += $taker_trade_saldo;
            $wallet_taker_base->balance += $taker_base_saldo;
            $wallet_taker_base->save();
            $wallet_taker_trade->save();
        } else if ($type == 'sell') {
            $taker_base_saldo = 0;
            $taker_trade_saldo = 0;
            if ($wallet_taker_trade->balance < $amount && $wallet_taker_trade->balance >= 0.00000001) {
                return ['data' => null, 'error' => true];
                //$amount = $wallet_taker_trade->balance;
            }
            $trades = \App\Trade::where('market_id',$market->id)->where("price", ">=", $price)->where('type', 'buy')->where('finished', false)->where("user_id", "<>", $user->id)->orderByDesc('price')->orderBy('id')->lockForUpdate()->get();
            if ($trades->isEmpty()) {
                $created_trade = addTrade($amount, $price, $type, $market, $user);
                return ['data' => ['fulfilled_amount' => $fulfilled_amount, 'created_trade' => $created_trade->toApi()], 'error' => false];

            }
            $all_fee = $amount * $price * $fee;
            if ($all_fee < 1e-8) {
                $all_fee = 1e-8;
            }
            $prices = $trades->groupBy('price');
            $sum = 0;
            $needed_trades = new \Illuminate\Support\Collection();
            foreach ($prices as $_price) {
                foreach ($_price as $trade) {
                    $sum += floatval(number_format($trade->amount, 8, '.', ''));
                    $needed_trades->push($trade);
                    if ($sum >= $amount) {
                        break;
                    }
                }
                if ($sum >= $amount) {
                    break;
                }
            }
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
                    $wallet_maker = \App\Wallet::where('user_id', $trade->user_id)->where("coin_id", $market->trade_currency_id)->lockForUpdate()->first();
                    $wallet_maker->balance += $trade->amount;
                    $amount -= $trade->amount;
                    $trade->user_id_taker = $user->id;
                    $trade->finished = true;
                    $trade->save();
                    $wallet_maker->save();
                    $ut = new \App\UserTrade();
                    $ut->trade_id = $trade->id;
                    $ut->user_id_maker = $trade->user_id;
                    $ut->user_id_taker = $user->id;
                    $ut->type = $trade->type;
                    $ut->price = $trade->price;
                    $ut->amount = $trade->amount;
                    $fulfilled_amount += $ut->amount;
                    $ut->market_id = $market->id;
                    $time = time();
                    list($usec, $sec) = explode(' ', microtime());
                    $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
                    $ut->created_at = $time;
                    $ut->updated_at = $time;
                    $points[] = new \InfluxDB\Point(
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
                    $events->push(new \App\Events\newUserTrade($ut));
                    $events->push(new \App\Events\TradeSuccessful($trade, $trade->amount));


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
                    $wallet_maker = \App\Wallet::where('user_id', $trade->user_id)->where("coin_id", $market->trade_currency_id)->lockForUpdate()->first();
                    $wallet_maker->balance += $amount;
                    $trade->amount -= $amount;
                    $amount1 = $amount;
                    $amount = 0;
                    if (floatval(number_format($trade->amount, 8, '.', '')) <= 0.00000001) {
                        $trade->finished = true;
                    }
                    $trade->save();

                    $wallet_maker->save();
                    $ut = new \App\UserTrade();
                    $ut->trade_id = $trade->id;
                    $ut->user_id_maker = $trade->user_id;
                    $ut->user_id_taker = $user->id;
                    $ut->type = $trade->type;
                    $ut->price = $trade->price;
                    $ut->amount = $amount1;
                    $fulfilled_amount += $ut->amount;
                    $ut->market_id = $market->id;
                    $time = time();
                    list($usec, $sec) = explode(' ', microtime());
                    $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
                    $ut->created_at = $time;
                    $ut->updated_at = $time;
                    $points[] = new \InfluxDB\Point(
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

                    $events->push(new \App\Events\newUserTrade($ut));
                    $events->push(new \App\Events\TradeSuccessful($trade, $amount1));
                }
            }
            $wallet_taker_trade->balance += $taker_trade_saldo;
            $wallet_taker_base->balance += $taker_base_saldo;
            $wallet_taker_base->save();
            $wallet_taker_trade->save();
        }

        if (floatval(number_format($amount, 8, '.', '')) >= 0.00000001) {
            $created_trade = addTrade($amount, $price, $type, $market, $user);
        }
        foreach ($events as $event) {
            event($event);
        }
        try {
            InfluxDB::writePoints($points, \InfluxDB\Database::PRECISION_MICROSECONDS);
        } catch (Exception $e) {

        };
        return ['data' => ['fulfilled_amount' => $fulfilled_amount, 'created_trade' => $created_trade->toApi()], 'error' => false];
    }, 1);
}

function addTrade($amount, $price, $type, $market, $user)
{

    return \Illuminate\Support\Facades\DB::transaction(function () use ($amount, $price, $type, $market, $user) {
        $trade_coin = Coin::find($market->trade_currency_id);
        $fee = $trade_coin->trading_fee->fee;
        $plus = 1 + $fee;
        $user_wallet = NULL;
        if ($type == "sell") {
            $user_wallet = \App\Wallet::where('user_id', $user->id)->where('coin_id', $market->trade_currency_id)->lockForUpdate()->first();
            if ($user_wallet->balance == 0) {
                return null;
            }
            if ($user_wallet->balance < $amount) {
                $amount = $user_wallet->balance;
            }
        } else
            if ($type == "buy") {
                $user_wallet = \App\Wallet::where('user_id', $user->id)->where('coin_id', $market->base_currency_id)->lockForUpdate()->first();
                if ($user_wallet->balance == 0) {
                    return null;
                }
                if ($user_wallet->balance < $amount * $price * $plus) {
                    $amount = floatval(number_format($user_wallet->balance / ($price * $plus)));
                }

            }
        if ($t = \App\Trade::create(["amount" => $amount, "price" => $price, "type" => $type, "market_id" => $market->id, "user_id" => $user->id, "fee" => $amount * $price * $fee])) {
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
            return $t;
        } else {
            return null;
        }
    }, 1);

}