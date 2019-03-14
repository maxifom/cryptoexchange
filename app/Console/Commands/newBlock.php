<?php

namespace App\Console\Commands;

use App\Coin;
use App\CoinFunding;
use App\CoinFundingDeposit;
use App\CoinInfo;
use App\Deposit;
use App\Market;
use App\News;
use App\User;
use App\Wallet;
use Denpa\Bitcoin\Client;
use Denpa\Bitcoin\Exceptions\BitcoindException;
use Illuminate\Console\Command;
use App\Withdrawal;
use App\Events\NewBlock as NewBlockEvent;
use App\Jobs\ProcessWithdrawal;
use Illuminate\Support\Facades\Storage;
use App\Events\TxConfirmation;
use App\Events\TxConfirmed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class newBlock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newBlock {coin} {block}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New block command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::transaction(function () {
            try {

                //$block = $this->argument('block');
                $coin = $this->argument('coin');
                //$coin_info = new CoinInfo();
                $c = Coin::where('name', $coin)->first();
                if (!$c) return 0;
                $bitcoind = new Client('http://' . $c->user . ':' . $c->pass . '@localhost:' . $c->port . '/');
                /*if ($c->coin_info == null) {
                    $coin_info->coin_id = $c->id;
                    $coin_info->block_count = $bitcoind->getBlockCount()->get();
                    $coin_info->last_block = $bitcoind->getBestBlockHash()->get();
                    $coin_info->block_time = date("Y-m-d H:i:s", $bitcoind->getBlock($coin_info->last_block)->get()['time']);
                    $coin_info->connections = $bitcoind->getConnectionCount()->get();
                    $coin_info->save();
                } else {
                    $coin_info = $c->coin_info;
                    $coin_info->last_block = $block;
                    $coin_info->block_count = $bitcoind->getBlockCount()->get();
                    $coin_info->block_time = date("Y-m-d H:i:s", $bitcoind->getBlock($block)->get()['time']);
                    $coin_info->connections = $bitcoind->getConnectionCount()->get();
                    $coin_info->save();
                }*/
                $wallets = Wallet::where('coin_id', $c->id)->get();

                $deposits =Deposit::whereIn("wallet_id", $wallets->pluck('id'))->where('confirmed', 0)->lockForUpdate()->get();

                foreach ($deposits as $deposit) {
                    if ($deposit->confirmations < $c->needed_confirmations && $deposit->confirmed === 0) {
                        $_tx = $bitcoind->getRawTransaction($deposit->tx, 1)->get();
                        if (isset($_tx['confirmations'])) {
                            $deposit->confirmations = $_tx['confirmations'];
                        } else {
                            $deposit->confirmations = 0;
                        }
                        if ($deposit->confirmations >= $c->needed_confirmations) {
                            $wallet = Wallet::where('id', $deposit->wallet_id)->lockForUpdate()->first();
                            $wallet->balance += $deposit->value;
                            $wallet->save();
                            $deposit->confirmed = 1;
                            $deposit->save();
                            event(new TxConfirmed($deposit));
                        } else {
                            $deposit->save();
                            event(new TxConfirmation($deposit));
                        }

                    }
                }
                $coin_funding = CoinFunding::where('coin_id',$c->id)->first();
                if ($coin_funding)
                {
                    $coin_funding_deposits = CoinFundingDeposit::where('funding_id',$coin_funding->id)->where('confirmed',0)->lockForUpdate()->get();
                    foreach ($coin_funding_deposits as $deposit) {
                        if ($deposit->confirmations < $coin_funding->needed_confirmations && $deposit->confirmed === 0) {
                            $_tx = $bitcoind->getRawTransaction($deposit->tx, 1)->get();
                            if (isset($_tx['confirmations'])) {
                                $deposit->confirmations = $_tx['confirmations'];
                            } else {
                                $deposit->confirmations = 0;
                            }
                            if ($deposit->confirmations >= $c->needed_confirmations) {
                                $coin_funding->amount += $deposit->value;
                                if ($coin_funding->amount>=$coin_funding->needed_amount)
                                {
                                    $coin_funding->funded=1;
                                    $c->status='confirmed';
                                    $c->save();
                                    $users = User::all();
                                    foreach ($users as $user) {
                                        if ($user->admin === 0) {
                                            $w = new Wallet;
                                            $w->user_id = $user->id;
                                            $w->coin_id = $c->id;
                                            $w->save();
                                        }

                                    };
                                    $m = new Market;
                                    $m->base_currency_id = Coin::where('name', "BTC")->first()->id;
                                    $m->trade_currency_id = $c->id;
                                    $m->save();
                                    $fee = $c->fee;
                                    News::create([
                                        'header' => 'New coin: ' . $c->name,
                                        'text' => "Name:" . $c->name . PHP_EOL . "Needed confirmations:" . $c->needed_confirmations . PHP_EOL . "Fee:" . $fee->fee
                                    ]);
                                }
                                $coin_funding->save();
                                $deposit->confirmed = 1;
                                $deposit->save();
                            } else {
                                $deposit->save();
                            }

                        }
                    }
                }

                $withdrawals = Withdrawal::where("status", "approved")->whereIn("wallet_id", $wallets->pluck('id'))->where('tx', NULL)->select('id')->get();
                foreach ($withdrawals as $withdrawal) {
                    if (!Storage::disk('local')->exists("current_withdrawals/" . $withdrawal->id)) {
                        Storage::disk('local')->put("current_withdrawals/" . $withdrawal->id, "1");
                        ProcessWithdrawal::dispatch($withdrawal->id);
                    }
                }
                return 1;
            } catch (BitcoindException $e) {
                report($e);
            }
        },1);
    }
}
