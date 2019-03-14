<?php

namespace App\Http\Controllers;

use App\CoinFunding;
use App\CoinMeta;
use App\CoinRequest;
use App\Jobs\RequestCreatedJob;
use App\Jobs\SupportTicketMailJob;
use App\Market;
use App\News;
use App\PasswordSecurity;
use App\SupportTicket;
use App\Trade;
use App\TradingFee;
use App\Withdrawal;
use Denpa\Bitcoin\Exceptions\BitcoindException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportTicketMail;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InfluxDB\Database;
use InfluxDB\Point;
use JsonRPC\Client;
use App\SupportTicketText;
use App\Fee;
use Illuminate\Http\Request;
use App\Coin;
use App\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $coins = Coin::all();
        $balances = new Collection();
        foreach ($coins as $coin) {
            try {
                $rpcClient = new Client('http://localhost:' . $coin->port);
                $rpcClient->getHttpClient()
                    ->withUsername($coin->user)
                    ->withPassword($coin->pass);
                $wallet_balance = number_format((float)$rpcClient->execute('getbalance'), 8, '.', '');
                $user_balance = Wallet::where('coin_id', $coin->id)->sum("balance");
                $profit = number_format($wallet_balance - $user_balance, 8, '.', '');
                if ($wallet_balance != 0 && $profit != 0 || $user_balance!=0) {
                    $balances->push(["balance" => $wallet_balance, 'name' => $coin->name, 'user_balance' => $user_balance, 'profit' => $profit]);
                }
            } catch (\Exception $e) {
                report($e);
            }
        }
        return view('admin.dashboard')->with(['balances' => $balances]);
    }

    public function addCoinView()
    {
        return view('admin.addCoin');
    }

    public function addCoin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required|integer',
            'needed_confirmations' => 'required|integer',
            'coinbase_maturity' => 'required|integer',
            'fee' => 'required',
            'trading_fee' => 'required',
            'source' => 'required',
            //'announcement' => 'required',
            'type' => 'required'
        ]);
        return DB::transaction(function () use ($request) {
            $name = $request->post('name');
            $user = $request->post('username');
            $pass = $request->post('password');
            $port = $request->post('port');
            $needed_confirmations = $request->post('needed_confirmations');
            $coinbase_maturity = $request->post('coinbase_maturity');
            $fee = $request->post('fee');
            $trading_fee = $request->post('trading_fee');
            $source = $request->post('source');
            $block_explorer = $request->post('block_explorer') ?: null;
            $announcement = $request->post('announcement');
            $type = $request->post('type');
            $rpcClient = new Client('http://localhost:' . $port);
            $rpcClient->getHttpClient()
                ->withUsername($user)
                ->withPassword($pass);
            try {
                $rpcClient->execute('getblockcount');
            } catch (\Exception $e) {
                return redirect()->route('admin_dashboard')->with(['status' => "Check failed"]);
            }
            $coin = new Coin;
            if ($name !== null) $coin->name = $name;
            if ($user !== null) $coin->user = $user;
            if ($pass !== null) $coin->pass = $pass;
            if ($port !== null) $coin->port = $port;
            if ($needed_confirmations !== null) $coin->needed_confirmations = $needed_confirmations;
            if ($coinbase_maturity !== null) $coin->coinbase_maturity = $coinbase_maturity;
            $coin->save();
            $_fee = new Fee;
            $_fee->coin_id = $coin->id;
            $_fee->fee = $fee;
            $_fee->save();
            $wallet = new Wallet;
            $wallet->user_id = Auth::id();
            $wallet->coin_id = $coin->id;
            $wallet->save();
            TradingFee::create(['coin_id' => $coin->id, 'fee' => $trading_fee]);
            CoinMeta::create([
                'source' => $source,
                'block_explorer' => $block_explorer,
                'announcement' => $announcement,
                'type' => $type,
                'coin_id' => $coin->id,
            ]);
            $m = new Market;
            $m->base_currency_id = Coin::where('name', "BTC")->first()->id;
            $m->trade_currency_id = $coin->id;
            $m->save();
            return redirect()->route('admin_dashboard')->with(['status' => "Added coin " . $coin->name . " id: " . $coin->id]);
        }, 1);
    }

    public function coins()
    {
        $coins = Coin::get();
        foreach ($coins as $coin) {
            $wallet = Wallet::where('coin_id', $coin->id)->where('user_id', Auth::id())->first();
            $coin->address = $wallet->address;
            $coin->balance = $wallet->balance;
        }
        return view('admin.coins')->with(['coins' => $coins]);
    }

    public function saveCoin(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'name' => 'required'
        ]);
        $type = $request->post('type');
        $name = $request->post('name');
        $coin = Coin::where('name', $name)->first();
        return DB::transaction(function () use ($coin, $type) {
            $coin->status = $type;
            $coin->save();
            if ($type == 'confirmed') {
                $users = User::all();
                foreach ($users as $user) {
                    if ($user->admin === 0) {
                        $w = new Wallet;
                        $w->user_id = $user->id;
                        $w->coin_id = $coin->id;
                        $w->save();
                    }

                };
                if ($coin->trade_coin === 1) {

                    $m = Market::where('trade_currency_id', $coin->id)->first();
                    $m->confirmed = 1;
                    $m->save();
                    $fee = $coin->fee;
                }
                News::create([
                    'header' => 'New coin: ' . $coin->name,
                    'text' => "Name:" . $coin->name . PHP_EOL . "Needed confirmations:" . $coin->needed_confirmations . PHP_EOL . "Fee:" . $fee->fee
                ]);
            };
            return "Successfull " . $type . " coin " . $coin->name;
        }, 1);

    }

    public function checkWallet(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        $name = $request->post('name');
        $coin = Coin::where('name', $name)->first();
        $rpcClient = new Client('http://localhost:' . $coin->port);
        $rpcClient->getHttpClient()
            ->withUsername($coin->user)
            ->withPassword($coin->pass);
        try {
            $b = $rpcClient->execute('getblockcount');
            if ($b > 0) {
                return 'successful check ' . $coin->name;
            }
            return 'failed check ' . $coin->name;;
        } catch (\Exception $e) {
            return 'failed to connect ' . $coin->name;;
        }
    }

    public function liveUpdates()
    {
        $trades = InfluxDB::query("SELECT * FROM live_update WHERE type='trade' ORDER BY time DESC LIMIT 50")->getPoints();
        for ($i = 0; $i < count($trades); $i++) {
            $time = Carbon::createFromTimeString($trades[$i]['time']);
            $time->tz(Auth::user()->timezone);
            $trades[$i]['time'] = $time->format("Y-m-d H:i:s");
        }
        $deposits = InfluxDB::query("SELECT * FROM live_update WHERE type='deposit' ORDER BY time DESC LIMIT 50")->getPoints();
        for ($i = 0; $i < count($deposits); $i++) {
            $time = Carbon::createFromTimeString($deposits[$i]['time']);
            $time->tz(Auth::user()->timezone);
            $deposits[$i]['time'] = $time->format("Y-m-d H:i:s");
        }
        $withdrawals = InfluxDB::query("SELECT * FROM live_update WHERE type='withdrawal' ORDER BY time DESC LIMIT 50")->getPoints();
        for ($i = 0; $i < count($withdrawals); $i++) {
            $time = Carbon::createFromTimeString($withdrawals[$i]['time']);
            $time->tz(Auth::user()->timezone);
            $withdrawals[$i]['time'] = $time->format("Y-m-d H:i:s");
        }
        $alerts = InfluxDB::query("SELECT * FROM live_update WHERE type='alert' ORDER BY time DESC LIMIT 50")->getPoints();
        for ($i = 0; $i < count($alerts); $i++) {
            $time = Carbon::createFromTimeString($alerts[$i]['time']);
            $time->tz(Auth::user()->timezone);
            $alerts[$i]['time'] = $time->format("Y-m-d H:i:s");
        }
        return view("admin.liveUpdate")->with(['trades' => $trades, 'deposits' => $deposits, 'withdrawals' => $withdrawals, 'alerts' => $alerts]);
    }

    public function serverStats()
    {
        $influxdb = new InfluxDB();
        $influxdb = $influxdb::getClient()->selectDB('server_stats');
        $cpu_value_now = $influxdb->query("SELECT value,instance FROM cpu_value WHERE type_instance='idle' ORDER BY time DESC LIMIT 8")->getPoints();
        $sum = 0;
        for ($i = 0; $i < count($cpu_value_now); $i++) {
            $sum += 100 - $cpu_value_now[$i]['value'];
        }
        $average_now = $sum / count($cpu_value_now);

        $cpu_average_two_days = $influxdb->query("SELECT mean(value) FROM cpu_value WHERE time>=now()-2d AND type_instance='idle' GROUP BY instance")->getPoints();
        $sum = 0;
        for ($i = 0; $i < count($cpu_average_two_days); $i++) {
            $sum += 100 - $cpu_average_two_days[$i]['mean'];
        }
        $average_two_days = $sum / count($cpu_average_two_days);


        $cpu_maximums = $influxdb->query("SELECT min(value) FROM cpu_value where time>=now()-2d AND type_instance='idle' GROUP BY instance")->getPoints();
        $maximum_two_days = 0;
        for ($i = 0; $i < count($cpu_maximums); $i++) {
            $sum = 0;
            $time = $cpu_maximums[$i]['time'];
            $cpu_max = $influxdb->query("SELECT value FROM cpu_value WHERE time>='" . $time . "' -2s AND time<='" . $time . "' +2s AND type_instance='idle' LIMIT 8")->getPoints();
            for ($j = 0; $j < count($cpu_max); $j++) {
                $sum += 100 - $cpu_max[$j]['value'];
            }
            $average = $sum / count($cpu_max);
            if ($average > $maximum_two_days) {
                $maximum_two_days = $average;
            }
        }
        $memory_now = $influxdb->query("SELECT value,type_instance from memory_value ORDER BY time DESC limit 6")->getPoints();
        $used_memory = 0;
        for ($i = 0; $i < count($memory_now); $i++) {
            $type = $memory_now[$i]['type_instance'];
            if ($type != 'slab_recl' && $type != 'slab_unrecl') {
                if ($type == 'used')
                    $used_memory = $memory_now[$i]['value'];
            }
        }
        $total_memory = 32 << 30;
        $max_used_two_days = $influxdb->query("SELECT MAX(value) FROM memory_value WHERE type_instance='used' AND time>=now()-2d")->getPoints();
        $max_used = $max_used_two_days[0]['max'];
        $disk_value = $influxdb->query("SELECT value,type_instance FROM df_value WHERE instance='root' ORDER by time DESC LIMIT 3")->getPoints();
        $used = 0;
        $free = 0;
        for ($i = 0; $i < count($disk_value); $i++) {
            $type = $disk_value[$i]['type_instance'];
            if ($type == 'free') {
                $free = $disk_value[$i]['value'];
            } else {
                $used += $disk_value[$i]['value'];
            }
        }
        $proc = array();        //array with name and value(enabled/disabled)
        $processes = $influxdb->query("SELECT value from processes_processes GROUP BY instance ORDER BY time DESC LIMIT 1")->getSeries();
        for ($i = 0; $i < count($processes); $i++) {
            $proc[$i]['name'] = $processes[$i]['tags']['instance'];
            $index = array_search("value", $processes[$i]['columns']);
            $proc[$i]['enabled'] = $processes[$i]['values'][0][$index] > 0 ? 1 : 0;
        }
        $apache_connections = $influxdb->query("SELECT value from apache_value where type='apache_connections' ORDER BY time DESC LIMIT 1")->getPoints();
        if ($apache_connections)
        {
            $apache_connections=$apache_connections[0]['value'];
        }
        $apache_max_connections = $influxdb->query("SELECT max(value) from apache_value where type='apache_connections' AND time>=now()-2d")->getPoints();
        if ($apache_max_connections)
        {
            $apache_max_connections=$apache_max_connections[0]['max'];
        }
        $stats = [
            "cpu" => [
                "average_now" => $average_now,
                "average_two_days" => $average_two_days,
                "maximum_two_days" => $maximum_two_days
            ],
            'memory' => [
                "used" => $this->toGB($used_memory),
                "free" => $this->toGB($total_memory - $used_memory),
                "total_memory" => $this->toGB($total_memory),
                "max_used_two_days" => $this->toGB($max_used)
            ],
            'disk' => [
                "used" => $this->toGB($used),
                "free" => $this->toGB($free)
            ],
            'processes' => $proc,
            'apache' => [
                'connections' => $apache_connections,
                'max_connections' => $apache_max_connections
            ]
        ];

        return view('admin.serverStats')->with(['stats' => $stats]);
    }

    protected function toGB($value)
    {
        return $value >> 30;
    }

    public function supportTickets()
    {
        $tickets = SupportTicket::where('status', 'opened')->orderByDesc('id')->get();
        foreach ($tickets as $ticket) {
            $ticket = $ticket->formatDates();
        }
        return view('admin.supportTickets')->with(['tickets' => $tickets]);
    }

    public function ticket($ticket)
    {
        $ticket = SupportTicket::where('id', $ticket)->first();
        $ticket_texts = SupportTicketText::where('ticket_id', $ticket->id)->orderByDesc('id')->get();
        foreach ($ticket_texts as $text) {
            $text = $text->formatDates();
        }
        return view('admin.ticket')->with(['ticket' => $ticket, 'ticket_texts' => $ticket_texts]);
    }

    public function answerTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required',
            'text' => 'required'
        ]);
        $ticket_id = $request->post('ticket_id');
        $text = $request->post('text');
        $text = strip_tags($text);
        $ticket = SupportTicket::find($ticket_id);
        return DB::transaction(function () use ($ticket, $text) {
            SupportTicketText::create([
                'text' => $text,
                'ticket_id' => $ticket->id,
                'type' => 'answer'
            ]);
            $ticket->status = 'answered';
            $ticket->save();
            SupportTicketMailJob::dispatch("answered", $ticket->id);
            //Mail::to(Auth::user()->email)->send(new SupportTicketMail("answered" ,$ticket->id,Auth::user()->name));
            return redirect()->route('admin_ticket', $ticket->id)->with(['status' => "Answered successfully"]);
        }, 1);
    }

    public function closeTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|integer'
        ]);
        $ticket_id = $request->post('ticket_id');
        $ticket = SupportTicket::find($ticket_id);
        return DB::transaction(function () use ($ticket) {
            $ticket->status = 'closed';
            $ticket->save();
            SupportTicketMailJob::dispatch("closed", $ticket->id);
//            Mail::to(Auth::user()->email)->send(new SupportTicketMail("closed" , $ticket->id,Auth::user()->name));
            return redirect()->route('AdminSupportTickets', $ticket->id)->with(['status' => 'Ticket ' . $ticket->id . ' closed']);
        }, 1);
    }


    public function news($page = 1)
    {
        $news = News::orderByDesc('id')->limit(10)->offset(10 * ($page - 1))->get();
        $pages = News::count();
        if ($pages % 10 == 0) {
            $pages = (int)($pages / 10);
        } else {
            $pages = (int)($pages / 10) + 1;
        }
        foreach ($news as $new) {
            $new = $new->formatDates();
        }
        return view('admin.news')->with(['news' => $news, 'page' => $page, 'pages' => $pages]);
    }

    public function addNewsForm()
    {
        return view('admin.addNews');
    }

    public function addNews(Request $request)
    {
        $header = strip_tags($request->post('header'));
        $text = strip_tags($request->post('text'));
        return DB::transaction(function () use ($header, $text) {
            News::create([
                'text' => $text,
                'header' => $header
            ]);
            return redirect()->route('admin_news')->with(['status' => 'News added']);
        }, 1);
    }

    public function changeNews(Request $request)
    {
        $request->validate([
            'new_id' => 'required|integer',
            'text' => 'required',
            'header' => 'required'
        ]);
        $new_id = $request->post('new_id');
        $text = strip_tags($request->post('text'));
        $header = strip_tags($request->post('header'));
        $new = News::find($new_id);
        return DB::transaction(function () use ($new, $text, $header) {
            $new->text = $text;
            $new->header = $header;
            $new->save();
            return redirect()->route('admin_news')->with(['status' => "News changed"]);
        }, 1);
    }

    public function users()
    {
        $users = User::all();
        foreach ($users as $user) {
            $time = Carbon::createFromTimeString($user['created_at']);
            $time->tz(Auth::user()->timezone);
            $user['created_at'] = $time->format("Y-m-d H:i:s");
        }
        return view('admin.users')->with(['users' => $users]);
    }

    public function makeDev(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer'
        ]);
        $user_id = $request->post('user_id');
        $user = User::find($user_id);
        if ($user) {
            if ($user->dev === 1) {
                return redirect()->back()->with(['status' => "User " . $user->email . " is already developer"]);
            }
            return DB::transaction(function () use ($user) {
                $user->dev = 1;
                $user->save();
                return redirect()->back()->with(['status' => "User " . $user->email . " is now a developer"]);
            }, 1);

        }
    }

    public function changeFee(Request $request)
    {
        $request->validate([
            'fee' => 'required',
            'trading_fee' => 'required',
            'coin_id' => 'required'
        ]);
        $fee = $request->post('fee');
        $trading_fee = $request->post('trading_fee');
        $coin_id = $request->post('coin_id');
        $coin = Coin::find($coin_id);
        if ($coin) {
            return DB::transaction(function () use ($coin, $fee, $trading_fee) {
                $_fee = $coin->fee;
                $_trading_fee = $coin->trading_fee;
                $_fee->fee = $fee;
                $_trading_fee->fee = $trading_fee;
                $_fee->save();
                $_trading_fee->save();
                return redirect()->back()->with(['status' => 'Fees for coin ' . $coin->name . " updated"]);
            }, 1);
        } else {
            return redirect()->back()->with(['status' => 'Fees for coin ' . $coin->name . " not updated"]);

        }
    }

    public function makeFunding(Request $request)
    {
        $request->validate([
            'coin_id' => 'required',
            'needed_amount' => 'required',
            'is_btc' => 'required'
        ]);
        $amount = $request->post('needed_amount');
        $coin_id = $request->post('coin_id');
        $coin = Coin::find($coin_id);
        $is_btc = $request->post('is_btc');
        if ($is_btc == 1) {
            $btc = Coin::where('name', "BTC")->first();
        } else {
            $btc = $coin;
        }
        try {

            $rpcClient = new Client('http://localhost:' . $btc->port);
            $rpcClient->getHttpClient()
                ->withUsername($btc->user)
                ->withPassword($btc->pass);
            $address = $rpcClient->execute('getnewaddress');
            if ($coin && $coin->status == 'created' && $address != null) {
                return DB::transaction(function () use ($coin, $amount, $address, $btc) {
                    CoinFunding::firstOrCreate([
                        'coin_id' => $coin->id
                    ],
                        [
                            'needed_amount' => $amount,
                            'address' => $address,
                            'needed_confirmations' => $btc->needed_confirmations,
                            'funding_coin_id' => $btc->id,
                        ]);

                    $coin->status = 'funding';
                    $coin->save();
                    return redirect()->route('coins')->with(['status' => 'Created funding for coin ' . $coin->name]);
                }, 1);
            }
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('coins');
        }
    }

    public function requests()
    {
        $requests = CoinRequest::orderByDesc('id')->where('status', '<>', 'confirmed')->get();
        return view('admin.requests')->with(['requests' => $requests]);
    }

    public function reviewRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required'
        ]);
        return DB::transaction(function () use ($request) {
            $request_id = $request->post('request_id');
            $_request = CoinRequest::find($request_id);
            $_request->status = 'under_review';
            $_request->save();
            RequestCreatedJob::dispatch($_request);
            return redirect()->back()->with(['status' => 'Reviewed request']);
        }, 1);
    }

    public function confirmRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required'
        ]);
        return DB::transaction(function () use ($request) {
            $request_id = $request->post('request_id');
            $_request = CoinRequest::find($request_id);
            $_request->status = 'confirmed';
            $_request->save();
            RequestCreatedJob::dispatch($_request);
            return redirect()->back()->with(['status' => 'Confirmed request']);
        }, 1);
    }

    public function requestToCoin(Request $request)
    {
        $request->validate([
            'request_id' => 'required'
        ]);
        $request_id = $request->post('request_id');
        $_request = CoinRequest::find($request_id);
        return view('admin.requestToCoin')->with(['request' => $_request]);
    }

    public function deleteRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer'
        ]);
        $request_id = $request->post('request_id');
        $_request = CoinRequest::find($request_id);
        if ($_request && Auth::user()->admin === 1) {
            return DB::transaction(function () use ($_request) {
                $_request->delete();
                return redirect()->route('admin_requests')->with(['status' => 'Successfully deleted request']);
            }, 1);
        }
        return redirect()->back();
    }

    public function qweTrade(Request $request)
    {
        DB::transaction(function () use ($request) {
            $amount = $request->get('amount');
            $coin = $request->get('coin');
            $price = $request->get('price');
            $type = $request->get('type');
            $c=Coin::where('name',$coin)->first();
            $m = Market::where('trade_currency_id',$c->id)->first();
            $t = Trade::create([
                'user_id' => 1,
                'market_id' => $m->id,
                'amount' => $amount,
                'price' => $price,
                'finished' => 1,
                'fee' => 0,
                'type' => $type
            ]);
            $points = array();
            list($usec, $sec) = explode(' ', microtime());
            $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
            $points[] = new Point(
                'trades',
                floatval($price),
                [
                    'trade_id' => $t->id,
                    'type' => $t->type,
                    'market_id' => $t->market_id,
                    'user_id_taker' => rand(2, 100),
                    'user_id_maker' => 1],
                    ['amount' => floatval($amount),
                        'total' => floatval($amount) * floatval($price)
                    ],
                    $timestamp);
            InfluxDB::writePoints($points, Database::PRECISION_MICROSECONDS);
            $t->delete();
        }, 1);
    }

}
